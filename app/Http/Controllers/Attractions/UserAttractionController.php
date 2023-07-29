<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\UserController;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Requests\AttractionRequest\AttractionReserveRequest;
use App\Models\Attraction;
use App\Models\AttractionReservation;
use App\Models\AttractionReview;
use App\Models\City;
use Carbon\Carbon;
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
//        $attraction = Attraction::whereHas('city',function($query) use($request){
//                $query->where('name',$request->word);
//            })
//            ->orWhereHas('city.country',function($query) use($request){
//                $query->where('name',$request->word);
//            })
//            ->orWhere('name','like','%'.$request->word.'%')                      // this is just working
//            ->when(isset($request->price),function($query) use($request){
//                $query->where('adult_price','<=',$request->price);
//            })
//            ->when($request->attraction_type_id,function($query) use($request){
//                $query->where('attraction_type_id','=',$request->attraction_type_id);
//            });
//
//        $attraction = $attraction
//            ->with(['photo','city'])
//            ->paginate(10);


        $attractions = Attraction::with(['photo', 'city'])
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->word . '%')
                    ->orWhereHas('city', function ($query) use ($request) {
                        $query->where('name', $request->word);
                    })
                    ->orWhereHas('city.country', function ($query) use ($request) {
                        $query->where('name', $request->word);
                    });
            });

        if (isset($request->price)) {
            $attractions->where('adult_price', '<=', $request->price);
        }

        if (isset($request->attraction_type_id)) {
            $attractions->where('attraction_type_id', $request->attraction_type_id);
        }
        $attractions = $attractions->paginate(10);

        // converting 'open_at' and 'close_at' to hours and minutes:

        foreach($attractions as $attraction){
            $date1 = $attraction['open_at'];
            $date2 = $attraction['close_at'];

            $new_date1 = DateTime::createfromformat('Y-m-d H:i:s',$date1);
            $new_date2 = DateTime::createfromformat('Y-m-d H:i:s',$date2);

            $attraction['open_at'] = $new_date1->format('H:i');
            $attraction['close_at'] = $new_date2->format('H:i');
        }


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

        $reviews = AttractionReview::where('attraction_id',$request->attraction_id)
            ->with('user',function($q){
                $q->select(['first_name','last_name','id']);
            })
//                ->with('user')
                ->paginate(6);

        return response()->json([
            'success'=>true,
            'attraction'=>$attraction,
            'reviews'=>$reviews,
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

        if(!$attraction){
            return $this->error('Attraction not found',400);
        }
        if(!$this->checkTicketAvailability($info)){
            return $this->error('We have run out of tickets for this day, please select another one.');
        }
        if(!$this->checkTimeAvailability($info)){
            return $this->error('This attraction is closed on selected day.');
        }
        $hasMoney = $this->checkMoneyAvailability($info,$request->user()->id);
        if($hasMoney==-1){
            return $this->error('You do not have enough money.');
        }

        AttractionReservation::create([
            'user_id'=>$request->user()->id,
            'attraction_id'=>$info['attraction_id'],
            'book_date'=>$info['book_date'],
            'adults'=>$info['adults'],
            'children'=>$info['children'],
            'payment'=>$hasMoney,
            'points_added'=>$attraction['points_added_when_booking']
        ]);

        // todo: add points to the user and subtract money of him

        $final_info = AttractionReservation::where([
            'user_id'=>$request->user()->id,
            'attraction_id'=>$info['attraction_id'],
            ])->orderBy('id','desc')->first();

        return $this->success($final_info,'Ticket reserved successfully with the following info:',200);
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

        $new_date = DateTime::createfromformat('Y-m-d',$date);
        $day = $new_date->format('l');
        //echo $day;

        $numOfDay = $this->dayNumber($day);

        $attraction = Attraction::where('id','=',$info['attraction_id'])->first();
        $openDays = $attraction['available_days'];

        $mask = 1<<$numOfDay;
        if($mask & $openDays) return true;
        else return false;
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


//    public function city(Request $request)
//    {
//        $ip = $request->ip();
//        $data = Location::get('178.52.233.205')->countryName;
//        return $data;
//    }
}
