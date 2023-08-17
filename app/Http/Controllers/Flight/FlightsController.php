<?php

namespace App\Http\Controllers\Flight;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\UserController;
use App\Models\Country;
use App\Models\Flights;
use App\Models\FlightsReservation;
use App\Models\FlightsTime;
use App\Models\FlightTravellers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use phpseclib3\Math\PrimeField\Integer;

class FlightsController extends UserController
{

    public function popularCountries()
    {
        $popularCountries = FlightsReservation::select('countries.id', 'countries.name','countries.path', DB::raw('count(*) as total'))
        ->join('flights_times', 'flights_reservations.flights_times_id', '=', 'flights_times.id')
        ->join('flights', 'flights_times.flights_id', '=', 'flights.id')
        ->join('countries', 'flights.distination', '=', 'countries.id')
        ->groupBy('countries.id', 'countries.name','countries.path')
        ->orderByDesc('total')
        ->take(5) // Get top 5 popular countries
        ->get();

         return response()->json([
        'message'=>"done",
        'popularCountries'=> $popularCountries,
         ]);
    }

    public function getCountries()
    {
        $countries = Country::select('id','name')
        ->get();

        return response()->json([
            'message' => "done",
            'countries' => $countries,
        ]);
    }

    public function searchFlights(Request $request)
    {
        $from = $request->input('from');
        $distination = $request->input('distination');
        $departe_day = $request->input('departe_day');
        $return_day = $request->input('return_day');
        $adults = $request->input('adults');
        $children = $request->input('children');

        $outboundFlights = Flights::select('flights.id as flight_id','flights_times.id as flights_times_id',  'flights_times.departe_day', 'flights.available_seats', 'flights.available_weight', 'country_from.name as from', 'country_to.name as to', 'flights_times.adults_price', 'flights_times.children_price','flights_times.From_hour', 'flights_times.To_hour','flights_times.duration'
            ,'airlines.name as airline_name','airlines.path as airline_photo','adults_price','children_price',
            DB::raw("'outbound_flights' as direction"))
//        DB::raw('(flights_times.adults_price  + flights_times.children_price ) as total_price'))
            ->join('flights_times', 'flights_times.flights_id', '=', 'flights.id')
            ->join('countries as country_from','flights.from','=','country_from.id')
            ->join('countries as country_to','flights.distination','=','country_to.id')
            ->join('airlines','flights.airline_id','=','airlines.id')
            ->where('country_from.name','LIKE', '%'.$from.'%')
            ->where('country_to.name','LIKE', '%'.$distination.'%')
            ->where('flights_times.departe_day', '>=', Carbon::parse($departe_day))
            ->where(function ($query) use ($adults, $children) {
        $query->where('flights.available_seats', '>=', $adults + $children);})
            ->orderByRaw('ABS(DATEDIFF(flights_times.departe_day, ?))', [$departe_day]) //to get the closest flights from the depart day
            ->take(4)->get()->toArray();

        //Return flights
        if(isset($return_day)&&$return_day!=null)
        {
        $returnFlights = Flights::select('flights.id as flight_id','flights_times.id as flights_times_id',  'flights_times.departe_day', 'flights.available_seats', 'flights.available_weight', 'country_from.name as from', 'country_to.name as to', 'flights_times.adults_price', 'flights_times.children_price','flights_times.From_hour', 'flights_times.To_hour','flights_times.duration'
            ,'airlines.name as airline_name','airlines.path as airline_photo','adults_price','children_price',
            DB::raw("'return_flights' as direction"))
//        DB::raw('(flights_times.adults_price  + flights_times.children_price ) as total_price'))
            ->join('flights_times', 'flights_times.flights_id', '=', 'flights.id')
            ->join('countries as country_from','flights.from','=','country_from.id')
            ->join('countries as country_to','flights.distination','=','country_to.id')
            ->join('airlines','flights.airline_id','=','airlines.id')
            ->where('country_from.name','LIKE', '%'.$distination.'%')
            ->where('country_to.name','LIKE', '%'.$from.'%')
            ->where('flights_times.departe_day', '>=',  Carbon::parse($return_day))
            ->where(function ($query) use ($departe_day) {
            $query->where(function ($query) use ($departe_day) {
                $query->whereDate('flights_times.departe_day', '>', Carbon::parse($departe_day))
                        ->orWhere(function ($query) use ($departe_day) {
                            $query->whereDate('flights_times.departe_day', '=', Carbon::parse($departe_day))
                                ->whereRaw('flights_times.From_hour >= ?', [Carbon::parse($departe_day)->format('H:i:s')])
                                ->whereRaw('flights_times.From_hour <= ?', [Carbon::parse($departe_day)->addHours(2)->format('H:i:s')]);
                        });
            });
        })
            ->where(function ($query) use ($adults, $children) {
            $query->where('flights.available_seats', '>=', $adults + $children);
        })
            ->take(4)->get()->toArray();


        foreach ($outboundFlights as &$flight) {
            $flight = ['outbound_flights' => $flight];
        }
        foreach ($returnFlights as &$flight) {
            $flight = ['return_flights' => $flight];
        }

        $flights = Arr::crossJoin($outboundFlights,$returnFlights);

        }
        else{

            foreach ($outboundFlights as &$flight) {
                $flight = ['outbound_flights' => $flight];
            }

            $returnFlights = [[]];
            $flights = Arr::crossJoin($outboundFlights,$returnFlights);
        }

        foreach ($flights as &$pair) {
            $outboundPriceForadults = $pair[0]['outbound_flights']['adults_price']*$adults;
            $outboundPriceForchildren = $pair[0]['outbound_flights']['children_price']*$children;
            $returnPriceForadults = isset($pair[1]['return_flights']) ? $pair[1]['return_flights']['adults_price']*$adults : 0;
            $returnPriceForchildren = isset($pair[1]['return_flights']) ? $pair[1]['return_flights']['children_price']*$children : 0;

            $totalPrice =$outboundPriceForadults+$outboundPriceForchildren+$returnPriceForadults+$returnPriceForchildren;
            $pair['total_price'] = $totalPrice;
        }


        return response()->json([
        'message' => "done",
        'final_flights' => $flights,
        ]);
    }

    /**
     * Booking Flight Tickets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookingTickets(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'check_or_book' => 'required|in:check,book',
            'flights_times_id_departure' => 'required',
            'flights_times_id_arrival' => 'required',
//            'flight_class' => 'required',
            'num_of_adults' => 'required',
            'num_of_children' => 'required',
            'with_discount'=> 'required_if:check_or_book,==,book|in:yes,no',
        ]);

        for($i=1 ; $i<=($request['num_of_adults']+$request['num_of_children']) ; $i++){
            $request->validate([
                'first_name'.$i =>'required',
                'last_name'.$i =>'required',
                'birth'.$i =>'required',
                'gender'.$i =>'in:male,female',
                'passport_id'.$i =>'required',
            ]);
        }

        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $flight_time_departure = FlightsTime::where('id', $request->flights_times_id_departure)->first();
        $flight_time_arrival = FlightsTime::where('id',$request->flights_times_id_arrival)->first();



        // ### 1 ### check if the ID is valid:
        if (!isset($flight_time_departure) || !isset($flight_time_arrival)) {
            return $this->error('Flight time not found.', 404);
        }
        $flight_departure = Flights::where('id', $flight_time_departure['flights_id'])->first();
        $flight_arrival = Flights::where('id',$flight_time_arrival['flights_id'])->first();

        // ### 2 ### check if there are tickets remains:
        if (!$this->checkTicketAvailability($request, $flight_time_departure, $flight_departure) || !$this->checkTicketAvailability($request,$flight_time_arrival,$flight_arrival)) {
            return $this->error('We have run out of tickets for this date.');
        }

        // ### 3 ### check money
        $money_needed = $this->checkMoneyAvailability($flight_time_departure,$flight_time_arrival,$request);
        if ($money_needed == -1) {
            return $this->error('You do not have enough money.');
        }

        // end of checks
        $one_dollar_equals = 0.01;

        $booking_info = [
            'user_id' => $request->user()->id,
            'flights_times_id_departure' => $request->flights_times_id_departure,
            'flights_times_id_arrival' => $request->flights_times_id_arrival,
//            'flight_class' => $request->flight_class,
            'num_of_adults' => $request->num_of_adults,
            'num_of_children' => $request->num_of_children,
            'payment' => $money_needed,
            'Points'=> (int)($money_needed * $one_dollar_equals),
        ];

        $one_point_equals = 10; // one point equals 10 dollars
        $discount = min($booking_info['payment'],$request->user()->points * $one_point_equals);
        $booking_info['payment_with_discount'] = $booking_info['payment']-$discount;

        if ($request->check_or_book == 'check') {
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
        } else {
            if($request->with_discount == 'yes' || $request->user()->wallet<$booking_info['payment']){
                $booking_info['payment'] = $booking_info['payment_with_discount'];
            }
            else{
                $discount = 0;
            }

            unset($booking_info['payment_with_discount']);

            // book for the two flights:
            $booking_info['flights_times_id'] = $booking_info['flights_times_id_departure'];
            FlightsReservation::create($booking_info);
            $booking_info['flights_times_id'] = $booking_info['flights_times_id_arrival'];
            FlightsReservation::create($booking_info);

            User::where('id',$request->user()->id)
                ->update([
                    'wallet'=> $request->user()->wallet - $booking_info['payment'],
                    'points'=> $request->user()->points - ($discount/$one_point_equals) + $booking_info['Points'],
                ]);

            $flight_reservation_departure = FlightsReservation::where('flights_times_id',$request['flights_times_id_departure'])->where('user_id',$request->user()->id)->orderBy('id','desc')->first();
            $flight_reservation_arrival = FlightsReservation::where('flights_times_id',$request['flights_times_id_arrival'])->where('user_id',$request->user()->id)->orderBy('id','desc')->first();

            for($i=1 ; $i<=($request['num_of_children']+$request['num_of_adults']) ; $i++){
                FlightTravellers::create([
                    'reservation_id'=>$flight_reservation_arrival['id'],
                    'first_name'=>$request->input('first_name'.$i),
                    'last_name'=>$request->input('last_name'.$i),
                    'birth'=>$request->input('birth'.$i),
                    'gender'=>$request->input('gender'.$i),
                    'passport_id'=>$request->input('passport_id'.$i),
                ]);
                FlightTravellers::create([
                    'reservation_id'=>$flight_reservation_departure['id'],
                    'first_name'=>$request->input('first_name'.$i),
                    'last_name'=>$request->input('last_name'.$i),
                    'birth'=>$request->input('birth'.$i),
                    'gender'=>$request->input('gender'.$i),
                    'passport_id'=>$request->input('passport_id'.$i),
                ]);
            }

            return $this->success($booking_info, 'Ticket reserved successfully with the following info:', 200);
        }
    }




    // helpful functions:

    protected function checkTicketAvailability($request,$flight_time,$flight): bool
    {
        $current_reservations = FlightsReservation::select([DB::raw('SUM(num_of_adults) as adults'),DB::raw('SUM(num_of_children) as children')])
            ->where('flights_times_id',$flight_time['id'])
            ->get();

        $all = $current_reservations[0]['adults'] + $current_reservations[0]['children'] + $request->num_of_adults + $request->num_of_children;

        if($all > $flight['available_seats']) return false;
        return true;
    }
    protected function checkMoneyAvailability($flight_time_departure,$flight_time_arrival,$request): int
    {
        $moneyNeeded = ($flight_time_departure['adults_price']+$flight_time_arrival['adults_price'])*$request->num_of_adults + ($flight_time_departure['children_price']+$flight_time_arrival['children_price'])*$request->num_of_children;

        if($this->checkWallet($moneyNeeded,$request->user()->id)){
            return $moneyNeeded;
        }
        else return -1;
    }

}
