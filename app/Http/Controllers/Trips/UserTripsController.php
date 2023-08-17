<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\UserController;
use App\Models\Trip;
use App\Models\TripDate;
use App\Models\TripDeparture;
use App\Models\TripFavourite;
use App\Models\TripOffer;
use App\Models\TripReview;
use App\Models\TripsReservation;
use App\Models\TripTraveler;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class UserTripsController extends UserController
{
    /**
     * Showing Main Page
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // top-rated trips
        $topRated = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
            ->availableTrips()
            ->orderBy('rate','desc')
            ->take(6)
            ->get();


        // most expensive trips
        $vip = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                $q->with(['country']);
                }
            ])
//            ->whereHas('departure',function($query){
//                $query->whereHas('dates',function($q){
//                    $q->where('price','>=',200);
//                });
//            })
            ->whereHas('dates',function($query){
                $query->where('price','>=',200);
            })
            ->availableTrips()
            ->take(6)
            ->get();


        // cheapest trips
        $cheapest = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
//            ->whereHas('departure',function($query){
//                $query->whereHas('dates',function($q){
//                    $q->where('price','<=',2000);
//                });
//            })
            ->whereHas('dates',function($q){
                $q->where('price','<=',2000);
            })
            ->availableTrips()
            ->take(6)
            ->get();


        //short trips
        $shortTrips = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
            ->where('days_number','<=',5)
            ->availableTrips()
            ->take(6)
            ->get();


        //long trips
        $longTrips = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
            ->where('days_number','>=',10)
            ->availableTrips()
            ->take(6)
            ->get();


//        $ip = $request->ip();                                        // getting the country from the ip address
//        $country = Location::get('178.52.233.205')->cityName;
//        $country = 'Syria';


        // local trips
        $local = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
            ->whereHas('destination',function($query){
                $query->whereHas('country',function($q){
                    $q->where('name','=','Syria');
                });
            })
            ->availableTrips()
            ->take(6)
            ->get();


        // trips with offers
        $trip_offers = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                },
                'offer',
            ])
            ->whereHas('offers',function($query){
                $time = Carbon::now();
                $query->where('offer_end','>=',$time)
                    ->where('active',true);
            })
            ->availableTrips()
            ->take(6)
            ->get();

        $topRated = $this->isMyFavourite($topRated,$request);
        $vip = $this->isMyFavourite($vip,$request);
        $cheapest = $this->isMyFavourite($cheapest,$request);
        $shortTrips = $this->isMyFavourite($shortTrips,$request);
        $longTrips = $this->isMyFavourite($longTrips,$request);
        $local = $this->isMyFavourite($local,$request);
        $trip_offers = $this->isMyFavourite($trip_offers,$request);


        return response()->json([
            'status'=>true,
            'topRated'=>$topRated,
            'VIP'=>$vip,
            'cheapest'=>$cheapest,
            'shortTrips'=>$shortTrips,
            'longTrips'=>$longTrips,
            'local'=>$local,
            'offers'=>$trip_offers,
        ]);
    }

    /**
     * Search For Some Trip
     * @param Request $request
     * @return JsonResponse
     */
    public function searchForTrip(Request $request): JsonResponse
    {
        $request->validate([
            'to'=>'required',
        ]);


        $trips = Trip::select(['id','destination','description','details','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
//            ->whereHas('destination',function($q)use($request){         // to city (mandatory)
//                $q->where('name','like','%'.$request->to.'%');
//            })
            ->where(function ($query) use ($request) {                           // specify destination: country, city or trip name.
                $query->where('description', 'like', '%' . $request->to . '%')
                    ->orWhereHas('destination', function ($query) use ($request) {
                        $query->where('name', 'like', '%'.$request->to.'%');
                    })
                    ->orWhereHas('destination.country', function ($query) use ($request) {
                        $query->where('name', 'like', '%'.$request->to.'%');
                    });
            })
            ->when($request->max_price,function($query)use($request){         // start price (filter, not mandatory)
                $query->whereHas('dates',function($q)use($request){
                    $q->where('price','<=',$request->max_price);
                });
            })
            ->when($request->min_price,function($query)use($request){         // start price (filter, not mandatory)
                $query->whereHas('dates',function($q)use($request){
                    $q->where('price','>=',$request->min_price);
                });
            })
            ->when($request->start_age,function($query)use($request){        // start age (filter, not mandatory)
                $query->where('start_age','<=',$request->start_age);
            })
            ->when($request->end_age,function($query)use($request){        // end age (filter, not mandatory)
                $query->where('end_age','>=',$request->end_age);
            })
            ->when($request->min_length,function($query)use($request){       // minimum length (filter, not mandatory)
                $query->where('days_number','>=',$request->min_length);
            })
            ->when($request->max_length,function($query)use($request){        // maximum length (filter, not mandatory)
                $query->where('days_number','<=',$request->max_length);
            })
            ->with(['photo',
                'destination',
                'destination.country'
            ])
            ->availableTrips()
            ->paginate(10);

        $trips = $this->isMyFavourite($trips,$request);

        return response()->json([
            'success'=>true,
            'data'=> $trips,
        ],200);
    }

    /**
     * View Trip Details
     * @param Request $request
     * @return JsonResponse
     */
    public function viewTripDetails(Request $request): JsonResponse
    {
        $trip = Trip::where('id',$request->id)
            ->with(['photos','destination','services','activities','days',
                'dates'=>function($query){
//                    $query->select(['id','departure_date','current_reserved_people','price',DB::raw('max_persons-current_reserved_people as remaining_seats')]);
                    $query->selectRaw('id, departure_date, trip_id, price');
                },
                'offers'=>function($query){
                    $time = Carbon::now();
                    $query->where('active',true)->where('offer_end','>=',$time)->latest();
                }])
            ->first();

        // checking if it is in user's favourites list
        if($request->hasHeader('Authorization')){
            $user = auth('user-api')->user();
            $favourite = TripFavourite::where('user_id',$user['id'])->where('trip_id',$trip['id'])->first();
            $trip['is_my_favourite'] = ( isset($favourite) ? 1:0);
        }
        else{
            $trip['is_my_favourite'] = 0;
        }

        $reviews = TripReview::select(['id','stars','comment','user_id'])
            ->where('trip_id',$request->id)
            ->with('user',function($q){
                $q->select(['id','first_name','last_name','photo']);
            })
            ->paginate(6);

        return response()->json([
            'success'=>true,
            'trip'=>$trip,
            'reviews'=>$reviews,
        ]);
    }

    // todo: add points to user account

    /**
     * Making Reservation
     * @param Request $request
     * @return JsonResponse
     */
    public function makeReservation(Request $request): JsonResponse
    {
        $request->validate([
            'date_id'=>'required',
            'adults'=>'required',
            'children'=>'required',
            'check_or_book'=> 'required|in:book,check',
            'with_discount'=> 'required_if:check_or_book,==,book|in:yes,no',
        ]);

        // validating variable data

        for($i=1 ; $i<=($request['adults']+$request['children']) ; $i++){
            $request->validate([
                'first_name'.$i =>'required',
                'last_name'.$i =>'required',
                'birth'.$i =>'required',
                'gender'.$i =>'in:male,female',
            ]);
        }

        $trip = Trip::whereHas('dates',function($q)use($request){
            $q->where('id',$request['date_id']);
        })->first();

        $date = TripDate::where('id',$request['date_id'])->first();


        // ### 1 ### check if there is at least one adult
        if($request->adults == 0){
            return $this->error('There should be at least one adult traveler');
        }

        // ### 2 ### check if the trip exist
        if(!isset($trip)){
            return $this->error('Date not found');
        }

        // ### 3 ### check if the trip has enough seats
        if(!$this->checkSeatAvailability($request,$trip,$date)){
            return $this->error('There are no enough seats');
        }

        // ### 4 ### check if the user has enough money
        $money_needed = $this->checkMoneyAvailability($request,$trip,$date);
        if($money_needed == -1){
            return $this->error('You do not have enough money');
        }

        // ### 5 ### check if the date hasn't passed yet
        if(!$this->checkTimeAvailability($date)){
            return $this->error('This date has already been passed');
        }

        // congratulations! finally, the booking process will proceed to the next step.

        $one_dollar_equals = 0.01;

        $booking_info = [
            'date_id'=>$date['id'],
            'user_id'=>$request->user()->id,
            'child'=>$request->children,
            'adult'=>$request->adults,
            'payment'=>$money_needed,
            'points_added'=> (int)($money_needed * $one_dollar_equals),
            'active'=>1,
        ];

        $one_point_equals = 10; // one point equals 10 dollars
        $discount = min($booking_info['payment'],$request->user()->points * $one_point_equals);
        $booking_info['payment_with_discount'] = $booking_info['payment']-$discount;

        if($request->check_or_book == 'check'){
            //return $this->success($booking_info,'When you press on book button, a ticket will be reserved with the following Info:');
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
            if($request->with_discount == 'yes' || $request->user()->wallet<$booking_info['payment']){
                $booking_info['payment'] = $booking_info['payment_with_discount'];
            }
            else{
                $discount = 0;
            }

            unset($booking_info['payment_with_discount']);
            TripsReservation::create($booking_info);

            // update current_reserved_people in dates table:

            TripDate::where('id',$date['id'])
                ->update([
                    'current_reserved_people'=> $date['current_reserved_people']+$request['adults']+$request['children'],
                ]);

            // todo: add points to user account

            User::where('id',$request->user()->id)
                ->update([
                    'wallet'=> $request->user()->wallet - $booking_info['payment'],
                    'points'=> $request->user()->points - ($discount/$one_point_equals) + $booking_info['points_added'],
                ]);


            // getting reservation to show it to user

            $trip_reservation = TripsReservation::where('date_id',$date['id'])->where('user_id',$request->user()->id)->orderBy('id','desc')->first();

            // adding travelers

            for($i=1 ; $i<=($request['adults']+$request['children']) ; $i++){
                TripTraveler::create([
                    'reservation_id'=>$trip_reservation['id'],
                    'first_name'=>$request->input('first_name'.$i),
                    'last_name'=>$request->input('last_name'.$i),
                    'birth'=>$request->input('birth'.$i),
                    'gender'=>$request->input('gender'.$i),
                ]);
            }

            return $this->success($booking_info,'Trip has been reserved with the last information');
        }
    }

    /**
     * Cancelling Reservation
     * @param Request $request
     * @return JsonResponse
     */
    public function cancellingReservation(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id'=>'required',
        ]);

        $reservation = TripsReservation::where('id',$request->reservation_id)->first();

        // ### 1 ### if there is no reservation with the previous id
        if(!isset($reservation)){
            return $this->error('Reservation not found.');
        }

        // ### 2 ### if reservation has already been cancelled
        if($reservation['active'] == 0){
            return $this->error('Reservation has already been cancelled');
        }

        // ### 3 ### if reservation doesn't belong to this user
        if($reservation['user_id'] != $request->user()->id){
            return $this->error('Unauthorized');
        }


        $res_time_plus_4 = $reservation['created_at']->addDay(4);
        $now = Carbon::now();

        // ### 4 ### A user can not cancel a reservation if it's been more than 4 days since he made it.
        if($now > $res_time_plus_4){
            return $this->error('You can not cancel the reservation, it has been more than 4 days since you made this one!');
        }

        $date = TripDate::where('id',$reservation['date_id'])->first();
        $departure_date = $date['departure_date'];
        $now = Carbon::now()->addDays(2);

        // ### 5 ### A user can not cancel a reservation if it remains less than two days to departure
        if($now > $departure_date){
            return $this->error('You can not cancel the reservation, it remains less than two days to departure!');
        }


        // Finally, reservation will be cancelled.


        // update current_reserved_people

        TripDate::where('id',$date['id'])
            ->update([
                'current_reserved_people'=> $date['current_reserved_people']-($reservation['adult']+$reservation['child']),
            ]);

        // update reservation to be not active
        TripsReservation::where('id',$reservation['id'])
            ->update([
                'active'=>0,
            ]);

        $user = User::where('id','=',$request->user()->id)->first();

        User::where('id','=',$user['id'])
            ->update([
                'wallet'=>$user['wallet']+$reservation['money_spent']*(9/10),
            ]);

        return $this->success('Reservation cancelled successfully!');

    }

    /**
     * Add Review
     * @param Request $request
     * @return JsonResponse
     */
    public function addReview(Request $request): JsonResponse
    {
        $request->validate([
            'stars'=>'required',
            'trip_id'=>'required',
        ]);

        $lastRate = TripReview::where([
            'user_id'=>$request->user()->id,
            'trip_id'=>$request->trip_id,
        ])->first();

        if($lastRate){
            return $this->error('you can not rate this trip more than one time');
        }

        $comment = null;
        if(isset($request->comment)){
            $comment = $request->comment;
        }

        TripReview::create([
            'stars'=>$request->stars,
            'comment'=>$comment,
            'user_id'=>$request->user()->id,
            'trip_id'=>$request->trip_id,
        ]);

        //recalculating the rate of the attraction

        $trip = Trip::where('id',$request->trip_id)->first();
        if(!$trip){
            return $this->error('trip not found');
        }

        $num_of_ratings = $trip['num_of_ratings'];
        $rate = $trip->rate;

        $new_rate = (($num_of_ratings*$rate)+$request->stars)/($num_of_ratings+1);

        Trip::where('id',$request->trip_id)
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
    private function checkSeatAvailability($request,$trip,$date): bool
    {
        return ($date['current_reserved_people']+$request['children']+$request['adults'] <= $trip['max_persons']);
    }
    private function checkMoneyAvailability($request,$trip,$date): int
    {
        $offer = TripOffer::where('trip_id',$trip['id'])->orderBy('id','desc')->first();
        $discount = 0;
        if(isset($offer)) $discount = $offer['percentage_off'];

        $trip_after_discount = $date['price']-($date['price']*$discount/100);
        $moneyNeeded = ($request->adults + $request->children) * $trip_after_discount;

        $user_id = $request->user()->id;
        if($this->checkWallet($moneyNeeded,$user_id)){
            return $moneyNeeded;
        }
        else return -1;
    }
    private function checkTimeAvailability($date): bool
    {
        $now = Carbon::now();
        return ($date['departure_date'] > $now);
    }

    private function isMyFavourite($trips,$request)
    {
        // for each trip, check if it is in user's favourites list

        if(!$request->hasHeader('Authorization')){
            foreach($trips as $trip){
                $trip['is_my_favourite'] = 0;
            }
            return $trips;
        }
        $user = auth('user-api')->user();
        foreach($trips as $trip){
            $bool = TripFavourite::where('user_id',$user['id'])->where('trip_id',$trip['id'])->first();
            $trip['is_my_favourite'] = ( isset($bool) ? 1:0);
        }
        return $trips;
    }

}
