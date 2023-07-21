<?php

namespace App\Http\Controllers\Flight;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\Country;
use App\Models\Flights;
use App\Models\FlightsReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        ->take(6) // Get top 5 popular countries
        ->get();

         return response()->json([
        'message'=>"done",
        'popularCountries'=> $popularCountries,
         ]);
    }
    
    public function searchFlights(Request $request)
    {
        $from = $request->input('from');
        $distination = $request->input('distination');
        $departe_day = $request->input('departe_day');
        $returnDay = $request->input('return_day');
        $adults = $request->input('adults');
        $children = $request->input('children');

        // Outbound flights
        $outboundFlights = Flights::select('flights.id', 'flights.from', 'flights.distination', 'flights_times.departe_day', 'flights.available_seats')
            ->join('flights_times', 'flights_times.flights_id', '=', 'flights.id')
            ->where('flights.from', 'like', '%' . $from . '%')
            ->where('flights.distination','like', '%' . $distination. '%')
            ->where('flights_times.departe_day', '=', $departe_day)
            ->where(function ($query) use ($adults, $children) {
                $query->where('flights.available_seats', '>=', $adults + $children);
            });

        // Return flights
        $returnFlights = Flights::select('flights.id', 'flights.from', 'flights.distination', 'flights_times.departe_day', 'flights.available_seats')
            ->join('flights_times', 'flights_times.flights_id', '=', 'flights.id')
            ->where('flights.from','like', '%' . $distination. '%')
            ->where('flights.distination', 'like', '%' . $from . '%')
            ->where('flights_times.departe_day', '=', $returnDay)
            ->where(function ($query) use ($adults, $children) {
                $query->where('flights.available_seats', '>=', $adults + $children);
            });

        $flights = $outboundFlights->union($returnFlights)->get();

        return response()->json([
            'message' => "done",
            'flights' => $flights,
        ]);
    }

    public function getCountries()
    {
        $countries = Country::select('name','path')
        ->get();

        return response()->json([
            'message' => "done",
            'countries' => $countries,
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Flights $flights)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Flights $flights)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Flights $flights)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Flights $flights)
    {
        //
    }
}
