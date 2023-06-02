<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelReview;
use App\Models\Room;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function ShowALLHotel() //done
    {
        $hotel = Hotel::all();

       return response()->json([
        'message'=>"done",
        'Hotels'=> $hotel,
       ]);
    }

    public function ShowHotelTypes() //done
    {
        $hotel = Hotel::orderBy('type_id','asc')
        ->take(15)
        ->get();
        $hotel = $hotel->makeHidden(['email','location','phone_number','details','website_url']);

       return response()->json([
        'message'=>"done",
        'Hotels'=> $hotel,
       ]);
    }

    public function ShowRoomsTypes() //done
    {
        $topRated = Room::orderBy('rate','desc')
        ->take(6)
        ->get();
        $topRated = $topRated->makeHidden(['details','created_at','updated_at']);

        $NonSmokingroom = Room::where('room_type','=',9)
        ->take(5)
        ->get();
        $NonSmokingroom = $NonSmokingroom->makeHidden(['details','created_at','updated_at']);

        $Accessibleroom = Room::where('room_type','=',6)
        ->take(5)
        ->get();
        
        $Accessibleroom = $Accessibleroom->makeHidden(['details','created_at','updated_at']);
    
        $Singlerooms = Room::where('room_type','=',4)
        ->take(5)
        ->get();
        
        $Singlerooms = $Singlerooms->makeHidden(['details','created_at','updated_at']);

        $suiet = Room::where('room_type','=',4)
        ->take(5)
        ->get();
        
        $suiet = $suiet->makeHidden(['details','created_at','updated_at']);

        return response()->json([
            'status'=>true,
            'topRated'=>$topRated,
            'NonSmokingroom'=>$NonSmokingroom,
            'Accessibleroom'=>$Accessibleroom,
            'Singlerooms'=>$Singlerooms,
            'suiet'=>$suiet,
        ]);
    }

    public function TopRated() //hotels /done
    {
        $topRated = Hotel::orderBy('rate','desc')
        ->with(['city'])
        ->take(6)
        ->get();

       $topRated = $topRated->makeHidden(['email','location','phone_number',
       'details','website_url','created_at','updated_at']);
       
       return response()->json([
            'message'=>"done",
            'Hotels'=> $topRated,
           ]);

    }

    public function ShowHotelRooms(Request $request) //params not body
    {
        $hotel_id = $request->hotel_id;
        $room = Room::where('hotel_id','=',$hotel_id)
        ->get();
        $room = $room->makeHidden(['details','created_at','updated_at']);


        return response()->json([
        'message'=>"done",
        'Rooms'=> $room,
        ]);
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
