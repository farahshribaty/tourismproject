<?php

namespace App\Http\Controllers\Flight;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\Country;
use App\Models\Flights;
use App\Models\FlightsReservation;
use App\Models\FlightsTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Nette\Utils\DateTime;
use Illuminate\Support\Carbon;

class FlightsController extends Controller
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

        $outboundFlights = Flights::select('flights.id as flight_id',  'flights_times.departe_day', 'flights.available_seats', 'country_from.name as from', 'country_to.name as to', 'flights_times.adults_price', 'flights_times.children_price','flights_times.From_hour', 'flights_times.To_hour','flights_times.duration'
        ,'airlines.name as airline_name',  DB::raw("'outbound flight' as direction"))
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
        $returnFlights = Flights::select('flights.id as flight_id',  'flights_times.departe_day', 'flights.available_seats', 'country_from.name as from', 'country_to.name as to', 'flights_times.adults_price', 'flights_times.children_price','flights_times.From_hour', 'flights_times.To_hour','flights_times.duration'
        ,'airlines.name as airline_name', DB::raw("'return flight' as direction"))
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
        })->take(4)->get()->toArray();

        foreach ($outboundFlights as &$flight) {
            $flight = ['outbound flights' => $flight];
        }
        foreach ($returnFlights as &$flight) {
            $flight = ['return flights' => $flight];
        }
        $flights = Arr::crossJoin($outboundFlights,$returnFlights);
        
        }
        else{
        $flights = $outboundFlights;
        }

        return response()->json([
        'message' => "done",
        'final flights' => $flights,
        ]);
    }
}
