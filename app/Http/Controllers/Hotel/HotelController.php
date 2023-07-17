<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelReview;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    public function ShowALLHotel() //done
    {
        $hotel = Hotel::orderBy('type_id','asc')
        ->with(['photos','city'])
        ->get();

       return response()->json([
        'message'=>"done",
        'Hotels'=> $hotel,
       ]);
    }

    public function ShowHotelTypes() //done
    {
        
        $hotel = Hotel::orderBy('type_id','asc')
        ->take(15)
        ->with(['photo','city'])
        ->get();
        $hotel = $hotel->makeHidden(['email','phone_number','details','website_url']);

       return response()->json([
        'message'=>"done",
        'Hotels'=> $hotel,
       ]);
    }

    public function ShowRoomsTypes() //done
    {
        $topRated = Room::orderBy('rate', 'desc')
        ->take(6)
        ->with(['photo', 'Hotel' => function ($query) {
            $query->select('id','name', 'location');
        }])
        ->get();

        $topRated = $topRated->makeHidden(['details','created_at','updated_at']);

        $NonSmokingroom = Room::where('room_type','=',9)
        ->take(5)
        ->with(['photo', 'Hotel' => function ($query) {
            $query->select('id','name', 'location');
        }])
        ->get();
        $NonSmokingroom = $NonSmokingroom->makeHidden(['details','created_at','updated_at']);

        $Accessibleroom = Room::where('room_type','=',6)
        ->take(5)
        ->with(['photo', 'Hotel' => function ($query) {
            $query->select('id','name', 'location');
        }])
        ->get();

        $Accessibleroom = $Accessibleroom->makeHidden(['details','created_at','updated_at']);

        $Singlerooms = Room::withAllInformation()
            ->where('room_types.name','=','Single rooms')
            ->take(6)
            ->with(['photo', 'Hotel' => function ($query) {
            $query->select('id','name', 'location');
        }])
        ->get();


        $suiet = Room::withAllInformation()
            ->where('room_types.name','=','Suiet')
            ->take(6)
            ->with(['photo', 'Hotel' => function ($query) {
            $query->select('id','name', 'location');
        }])
        ->get();

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
        ->with(['photo','city'])
        ->take(6)
        ->get();

       $topRated = $topRated->makeHidden(['email','location','phone_number',
       'details','website_url','created_at','updated_at']);

       return response()->json([
            'message'=>"done",
            'Hotels'=> $topRated,
           ]);

    }

    public function ShowHotelRooms(Request $request) //done
    {
        $hotel_id = $request->hotel_id;

        $rooms = Room::select('rooms.*', 'hotels.location')
        ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
        ->where('rooms.hotel_id', '=', $hotel_id)
        ->with(['photo'])
        ->get();

        $rooms = $rooms->makeHidden(['details','created_at','updated_at']);
    
        return response()->json([
        'message'=>"done",
        'Room:'=>$rooms
        ]);
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
