<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function ShowALLHotel(Request $request)
    {
        $hotel = Hotel::all();

       return response()->json([
        'message'=>"done",
        'Hotels'=> $hotel,
       ]);
    }

    public function ShowHotelTypes(Request $request)
    {
        $hotel = ;

       return response()->json([
        'message'=>"done",
        'Hotels'=> $hotel,
       ]);
    }
    

    public function HotelRating()
    {
        //
    }

    public function TopRated()
    {
        //
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        //
    }

    
    public function show(Hotel $hotel)
    {
        //
    }

    
    public function edit(Hotel $hotel)
    {
        //
    }

   
    public function update(Request $request, Hotel $hotel)
    {
        //
    }

    public function destroy(Hotel $hotel)
    {
        //
    }
}
