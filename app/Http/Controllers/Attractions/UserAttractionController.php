<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\UserController;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Requests\AttractionRequest\AttractionReserveRequest;
use App\Models\Attraction;
use App\Models\AttractionFavourite;
use App\Models\AttractionReservation;
use App\Models\AttractionReview;
use App\Models\City;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nette\Utils\DateTime;
use Stevebauman\Location\Facades\Location;

class UserAttractionController extends UserController
{

    /**
     * Main Page For Attractions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse             //wrong!
    {
        $topRated = Attraction::select(['id','city_id','name','rate','num_of_ratings','adult_price','child_price'])
            ->orderBy('rate','desc')
            ->with(['photo','city'])
            ->take(6)
            ->get();


        $paid = Attraction::select(['id','city_id','name','rate','num_of_ratings','adult_price','child_price'])
            ->orderBy('adult_price','desc')
            ->where('adult_price','>',0)
            ->with(['photo','city'])
            ->take(6)
            ->get();


        $free = Attraction::select(['id','city_id','name','rate','num_of_ratings','adult_price','child_price'])
            ->where('adult_price','=',0)
            ->with(['photo','city'])
            ->take(6)
            ->get();

        $topRated = $this->isMyFavourite($topRated,$request);
        $paid = $this->isMyFavourite($paid,$request);
        $free = $this->isMyFavourite($free,$request);


        return response()->json([
            'status'=>true,
            'topRated'=>$topRated,
            'paid'=>$paid,
            'free'=>$free,
        ]);
    }

    /**
     * Search For Attractions With Specific Criteria
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchForAttractions(Request $request): JsonResponse
    {
        $attractions = Attraction::with(['photo', 'city'])
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->word . '%')
                    ->orWhereHas('city', function ($query) use ($request) {
                        $query->where('name', 'like','%'.$request->word.'%');
                    })
                    ->orWhereHas('city.country', function ($query) use ($request) {
                        $query->where('name', 'like', '%'.$request->word.'%');
                    });
            });

        if (isset($request->price)) {
            $attractions->where('adult_price', '<=', $request->price);
        }

        if (isset($request->attraction_type_id)) {
            $attractions->where('attraction_type_id', $request->attraction_type_id);
        }

        if(isset($request->country_id)){
            $attractions->whereHas('city',function($q)use($request){
                $q->where('country_id',$request->country_id);
            });
        }
        $attractions = $attractions->paginate(10);

        // converting 'open_at' and 'close_at' to hours and minutes:
        $attractions = $this->adjustTime($attractions);

        // sending whether the attraction is in the user's favourites list ( just if the user is signed in)
        $attractions = $this->isMyFavourite($attractions,$request);


        return response()->json([
            'success'=>true,
            'data'=>$attractions,
        ],200);
    }

    /**
     * View Attraction Details
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function viewAttractionDetails(Request $request): JsonResponse
    {
        $request->validate([
            'attraction_id'=>'required',
        ]);

        $attraction = Attraction::where('id',$request->attraction_id)
            ->with(['city','type','photos'])
            ->first();

        $attraction['available_days'] = $this->convertBitmasktoWeekArray($attraction['available_days']);

        $city_id = $attraction['city_id'];

        $reviews = AttractionReview::where('attraction_id',$request->attraction_id)
            ->with('user',function($q){
                $q->select(['id','first_name','last_name','photo']);
            })
            ->paginate(6);


        $you_may_also_like = Attraction::select(['id','city_id','name','rate','num_of_ratings','adult_price','child_price'])
            ->where('city_id','=',$city_id)
            ->with(['photo','city'])
            ->take(6)
            ->get();

        // checking if it is in user's favourites list
        if($request->hasHeader('Authorization')){
            $user = auth('user-api')->user();
            $favourite = AttractionFavourite::where('user_id',$user['id'])->where('attraction_id',$attraction['id'])->first();
            $attraction['is_my_favourite'] = ( isset($favourite) ? 1:0);
        }
        else{
            $attraction['is_my_favourite'] = 0;
        }

        return response()->json([
            'success'=>true,
            'attraction'=>$attraction,
            'reviews'=>$reviews,
            'you_may_also_like'=>$you_may_also_like,
        ]);
    }

    /**
     * Booking Ticket Function
     *
     * @param AttractionReserveRequest $request
     * @return JsonResponse
     */
    public function bookingTicket(AttractionReserveRequest $request): JsonResponse
    {
        $info = $request->validated();

        $attraction = Attraction::where('id','=',$info['attraction_id'])->first();

        // ### 1 ### check if the ID is valid
        if(!$attraction){
            return $this->error('Attraction not found',400);
        }
        // ### 2 ### check if there are tickets remains
        if(!$this->checkTicketAvailability($info)){
            return $this->error('We have run out of tickets for this day, please select another one.');
        }
        // ### 3 ### check if the time & date are valid
        if(!$this->checkTimeAvailability($info)){
            return $this->error('This attraction is closed on selected day/time.');
        }
        // ### 4 ### check if user has money
        $hasMoney = $this->checkMoneyAvailability($info,$request->user()->id);
        if($hasMoney==-1){
            return $this->error('You do not have enough money.');
        }
        // end of checks.

        $booking_info = [
            'user_id'=>$request->user()->id,
            'attraction_id'=>$info['attraction_id'],
            'book_date'=>$info['book_date'],
            'adults'=>$info['adults'],
            'children'=>$info['children'],
            'payment'=>$hasMoney,
            'points_added'=>$attraction['points_added_when_booking'],
        ];

        $one_point_equals = 10; // one point equals 10 dollars
        $discount = min($booking_info['payment'],$request->user()->points * $one_point_equals);
        $booking_info['payment_with_discount'] = $booking_info['payment']-$discount;

        if($request->check_or_book == 'check'){
            if($request->user()->points == 0){
                unset($booking_info['payment_with_discount']);
                return $this->success($booking_info,'When you press on book button, a ticket will be reserved with the following Info:');
            }
            else{
                return response()->json([
                    'message'=> 'When you press on book button, a ticket will be reserved with the following Info:',
                    'data'=> $booking_info,
                    'message1'=> 'Would you like to get benefit of your points?',
                ]);
            }
        }
        else{
            if($request->with_discount == 'yes'){
                $booking_info['payment'] = $booking_info['payment_with_discount'];
            }
            else{
                $discount = 0;
            }

            unset($booking_info['payment_with_discount']);
            AttractionReservation::create($booking_info);

            // todo: add points to the user and subtract money of him

            User::where('id',$request->user()->id)
                ->update([
                    'wallet'=> $request->user()->wallet - $booking_info['payment'],
                    'points'=> $request->user()->points - ($discount/$one_point_equals),
                ]);

            return $this->success($booking_info,'Ticket reserved successfully with the following info:',200);
        }
    }

    /**
     * Sending Reviews For Some Attraction
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addReview(Request $request): JsonResponse
    {
        $request->validate([
            'stars'=>'required',
            'attraction_id'=>'required',
        ]);

        $lastRate = AttractionReview::where([
            'user_id'=>$request->user()->id,
            'attraction_id'=>$request->attraction_id,
        ])->first();

        if($lastRate){
            return response()->json([
                'success'=>false,
                'message'=>'you can not rate this attraction more than one time',
            ]);
        }

        $comment = null;
        if(isset($request->comment)){
            $comment = $request->comment;
        }

        AttractionReview::create([
            'stars'=>$request->stars,
            'comment'=>$comment,
            'user_id'=>$request->user()->id,
            'attraction_id'=>$request->attraction_id,
        ]);

        //recalculating the rate of the attraction

        $attraction = Attraction::where('id',$request->attraction_id)->first();
        if(!$attraction){
            return response()->json([
                'success'=>false,
                'message'=>'attraction not found',
            ]);
        }

        $num_of_ratings = $attraction['num_of_ratings'];
        $rate = $attraction->rate;

        $new_rate = (($num_of_ratings*$rate)+$request->stars)/($num_of_ratings+1);

        Attraction::where('id',$request->attraction_id)
            ->update([
            'rate'=> $new_rate,
            'num_of_ratings'=> $num_of_ratings+1,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'review has been sent successfully',
        ]);
    }


    // helpful functions
    private function checkMoneyAvailability($info,$user_id): int
    {
        $attraction = Attraction::where('id','=',$info['attraction_id'])->first();
        $moneyNeeded = $info['adults']*$attraction['adult_price'] + $info['children']*$attraction['child_price'];

        if($this->checkWallet($moneyNeeded,$user_id)){
            return $moneyNeeded;
        }
        else return -1;
    }
    private function checkTimeAvailability($info): bool
    {
        $date = $info['book_date'];

        $new_date = Carbon::createFromFormat('Y-m-d h:i:s',$date);
        $day = $new_date->format('l');
        $hour = $new_date->format('h:i');
        $hour_plus = Carbon::createFromFormat('Y-m-d h:i:s',$date)->addHour()->format('h:i');

        $attraction = Attraction::where('id','=',$info['attraction_id'])->first();

        // check hour availability
        $open = DateTime::createfromformat('Y-m-d h:i:s',$attraction['open_at']);
        $close = DateTime::createfromformat('Y-m-d h:i:s',$attraction['close_at']);
        $open = $open->format('h:i');
        $close = $close->format('h:i');
        if($hour<$open || $hour_plus>$close) return false;

        // check day availability
        $numOfDay = $this->dayNumber($day);
        $openDays = $attraction['available_days'];
        $mask = 1<<$numOfDay;
        if($mask & $openDays) return true;
        return false;
    }
    private function checkTicketAvailability($info): bool
    {
        $inSameDays = AttractionReservation::where('book_date','=',$info['book_date'])->get();

        $child = 0;
        $adult = 0;

        foreach($inSameDays as $inSameDay) {
            $child += $inSameDay['children'];
            $adult += $inSameDay['adults'];
        }
        $attraction = Attraction::where('id','=',$info['attraction_id'])->first();
        $childCapacity = $attraction['child_ability_per_day'];

        if($child+$info['children']>$attraction['child_ability_per_day']) return false;

        if($adult+$info['adults']>$attraction['adult_ability_per_day']) return false;

        return true;
    }

    private function adjustTime($attractions)
    {
        // it converts the time in each attraction to hours and minutes
        foreach($attractions as $attraction){
            $date1 = $attraction['open_at'];
            $date2 = $attraction['close_at'];

            $new_date1 = DateTime::createfromformat('Y-m-d H:i:s',$date1);
            $new_date2 = DateTime::createfromformat('Y-m-d H:i:s',$date2);

            $attraction['open_at'] = $new_date1->format('H:i');
            $attraction['close_at'] = $new_date2->format('H:i');
        }
        return $attractions;
    }
    private function isMyFavourite($attractions,$request)
    {
        // for each trip, check if it is in user's favourites list

        if(!$request->hasHeader('Authorization')){
            foreach($attractions as $attraction){
                $attraction['is_my_favourite'] = 0;
            }
            return $attractions;
        }
        $user = auth('user-api')->user();
        foreach($attractions as $attraction){
            $bool = AttractionFavourite::where('user_id',$user['id'])->where('attraction_id',$attraction['id'])->first();

            $attraction['is_my_favourite'] = ( isset($bool) ? 1:0);
        }
        return $attractions;
    }

}
