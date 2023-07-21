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

    public function searchForTrip(Request $request): JsonResponse
    {
        $request->validate([
            'to'=>'required',
        ]);


        $trips = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])

            ->whereHas('destination',function($q)use($request){         // to city (mandatory)
                $q->where('name','like','%'.$request->to.'%');
            })
//
//            ->whereHas('departure',function($query)use($request){        // form city (mandatory)
//                $query->whereHas('city',function($q)use($request){
//                    $q->where('name','like','%'.$request->from.'%');
//                });
//            })

//            ->when($request->min_price,function($query)use($request){        // start price (filter, not mandatory)
//                $query->whereHas('departure',function($que)use($request){
//                    $que->whereHas('dates',function($q)use($request){
//                        $q->where('price','>=',$request->min_price);
//                    });
//                });
//            })

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

//            ->when($request->max_price,function($query)use($request){        // end price (filter, not mandatory)
//                $query->whereHas('departure',function($que)use($request){
//                    $que->whereHas('dates',function($q)use($request){
//                        $q->where('price','<=',$request->max_price);
//                    });
//                });
//            })

//            ->when($request->max_price,function($query)use($request){          // end price (filter, not mandatory)
//                $query->whereHas('dates',function($q)use($request){
//                    $q->where('price','<=',$request->max_price);
//                });
//            })

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

            ->availableTrips()

            ->paginate(10);

        return response()->json([
            'success'=>true,
            'data'=> $trips,
        ],200);
    }

    public function viewTripDetails(Request $request): JsonResponse
    {
        $trip = Trip::where('id',$request->id)
            ->with(['photos','destination','services','activities','days',
//                'departure'=>function($query){
//                    $query->select(['id','trip_id','flight_id','departure_details','city_id'])
//                    ->with(['city','dates']);
//                },
                'dates'=>function($query){
//                    $query->select(['id','departure_date','current_reserved_people','price',DB::raw('max_persons-current_reserved_people as remaining_seats')]);
                    $query->selectRaw('id, departure_date, trip_id, price');
                },
                'offers'=>function($query){
                    $time = Carbon::now();
                    $query->where('active',true)->where('offer_end','>=',$time)->latest();
                }])
            ->first();

        $reviews = TripReview::select(['id','stars','comment','user_id'])
            ->where('trip_id',$request->id)
            ->with('user',function($q){
                $q->select(['id','first_name','last_name']);
            })
            ->take(3)
            ->get();

        return response()->json([
            'success'=>true,
            'trip'=>$trip,
            'reviews'=>$reviews,
        ]);
    }

    // todo: send prices after discount!

//    public function viewDeparturesAndDatesForSomeTrip(Request $request): JsonResponse
//    {
//        $request->validate([
//            'trip_id'=>'required',
//        ]);
//
//        $trip = Trip::where('id',$request->trip_id)->first();
//        if($trip == null){
//            return response()->json([
//                'success'=>false,
//                'message'=>'Trip not found',
//            ]);
//        }
//
//        $departures = TripDeparture::select(['trip_departures.id','trip_departures.flight_id','trip_departures.departure_details','cities.name as from_city'])
//            ->join('cities','cities.id','=','trip_departures.city_id')
//            ->where('trip_departures.trip_id',$request->trip_id)
//            ->with(['dates' => function($q)use($trip){
//                $date = Carbon::now()->addDay();
//                $q->select([DB::raw($trip['max_persons'].' - current_reserved_people as available_seats'),'id','departure_id','departure_date','price'])
//                    ->where('departure_date','>=',$date)                              // just dates form now on, and that have available seats
//                    ->where('current_reserved_people','<',$trip['max_persons']);
//            }])
//            ->get();
//
//        return response()->json([
//            'success'=>true,
//            'departures'=>$departures,
//        ]);
//    }

    // todo: add points to user account
    public function makeReservation(Request $request): JsonResponse
    {
        $request->validate([
            'date_id'=>'required',
            'adults'=>'required',
            'children'=>'required',
        ]);

        // validating variable data

        for($i=1 ; $i<=($request['adults']+$request['children']-1) ; $i++){
            $request->validate([
                'first_name'.$i =>'required',
                'last_name'.$i =>'required',
                'birth'.$i =>'required',
                'gender'.$i =>'required',
            ]);
        }


//        $trip = Trip::whereHas('departure',function($query)use($request){
//            $query->whereHas('dates',function($q)use($request){
//                $q->where('id',$request['date_id']);
//            });
//        })->first();

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

        // ### 4 ### check if the user have enough money
        $money_needed = $this->checkMoneyAvailability($request,$trip,$date);
        if($money_needed == -1){
            return $this->error('You do not have enough money');
        }

        // ### 5 ### check if the date haven't passed yet
        if(!$this->checkTimeAvailability($date)){
            return $this->error('This date has already been passed');
        }

        // congratulations! finally, the booking process will proceed to the next step.

        TripsReservation::create([
            'date_id'=>$date['id'],
            'user_id'=>$request->user()->id,
            'child'=>$request->children,
            'adult'=>$request->adults,
            'money_spent'=>$money_needed,
            'points_added'=>10,
            'active'=>1,
        ]);


        // update current_reserved_people in date:

        TripDate::where('id',$date['id'])
            ->update([
            'current_reserved_people'=> $date['current_reserved_people']+$request['adults']+$request['children'],
            ]);


        // todo: add points to user account and erase money form his wallet
        $user = User::where('id',$request->user()->id)->first();
        User::where('id','=',$user['id'])
            ->update([
                'wallet'=>$user['wallet']-$money_needed,
            ]);


        // getting reservation to show it to user

        $reservation = TripsReservation::where('date_id',$date['id'])->where('user_id',$request->user()->id)->orderBy('id','desc')->first();
        $reservation['date of departure']=$date['departure_date'];


        // adding travelers

        for($i=1 ; $i<=($request['adults']+$request['children']-1) ; $i++){
            TripTraveler::create([
                'reservation_id'=>$reservation['id'],
                'first_name'=>$request->input('first_name'.$i),
                'last_name'=>$request->input('last_name'.$i),
                'birth'=>$request->input('birth'.$i),
                'gender'=>$request->input('gender'.$i),
            ]);
        }

        return $this->success($reservation,'Trip has been reserved with the last information');
    }

    public function cancellingReservation(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_id'=>'required',
        ]);

        $reservation = TripsReservation::where('id',$request->reservation_id)->first();

        // ### 1 ### if there is no reservation with the last id
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


        // Finally, reservation will be cancelled


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
                'wallet'=>$user['wallet']+$reservation['money_spent'],
            ]);

        return $this->success('Reservation cancelled successfully!');

    }

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

    public function addToFavourites(Request $request): JsonResponse
    {
        $request->validate([
            'trip_id'=>'required',
        ]);

        TripFavourite::create([
            'user_id'=> $request->user()->id,
            'trip_id'=> $request->trip_id,
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Trip added to favourites successfully',
        ]);
    }

    public function removeFromFavourites(Request $request): JsonResponse
    {
        $request->validate([
            'trip_id'=>'required',
        ]);

        TripFavourite::where('user_id','=',$request->user()->id)
            ->where('trip_id','=',$request->trip_id)
            ->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Trip removed from favourites successfully',
        ]);
    }

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

}
