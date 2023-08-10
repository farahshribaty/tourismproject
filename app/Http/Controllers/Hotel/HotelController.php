<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HotelController extends Controller
{
    public function ShowALLHotel() //done
    {
        $hotel = Hotel::orderBy('type_id','asc')
        ->with(['photo','city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($q) {
                $q->select('id','name');
            }]);
        }]) 
        ->get();

       return response()->json([
        'message'=>"done",
        'Hotels'=> $hotel,
       ]);
    }

    public function ShowHotelTypes() //done //not used
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
            $query->select('id','name', 'location','city_id')
            ->with(['City' => function ($q) {
                $q->select('id','name');
            }]);
        }])
        ->get();


        $topRated = $topRated->makeHidden(['details','created_at','updated_at']);

        $NonSmokingroom = Room::where('room_type','=',9)
        ->take(5)
        ->with(['photo', 'Hotel' => function ($query) {
            $query->select('id','name', 'location','city_id')
            ->with(['City' => function ($q) {
                $q->select('id','name');
            }]);
        }])
        ->get();
        $NonSmokingroom = $NonSmokingroom->makeHidden(['details','created_at','updated_at']);

        $Accessibleroom = Room::where('room_type','=',6)
        ->take(5)
        ->with(['photo', 'Hotel' => function ($query) {
            $query->select('id','name', 'location','city_id')
            ->with(['City' => function ($q) {
                $q->select('id','name');
            }]);
        }])
        ->get();

        $Accessibleroom = $Accessibleroom->makeHidden(['details','created_at','updated_at']);

        $Singlerooms = Room::where('room_type','=',4)
            ->take(6)
            ->with(['photo', 'Hotel' => function ($query) {
                $query->select('id','name', 'location','city_id')
                ->with(['City' => function ($q) {
                    $q->select('id','name');
                }]);
        }])
        ->get();

        $Singlerooms = $Singlerooms->makeHidden(['details','created_at','updated_at']);

        $suiet = Room::where('room_type','=',2)
            ->take(6)
            ->with(['photo', 'Hotel' => function ($query) {
                $query->select('id','name', 'location','city_id')
                ->with(['City' => function ($q) {
                    $q->select('id','name');
                }]);
        }])
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

    public function TopRatedAndTypes() //hotels /done
    {
        $topRated = Hotel::orderBy('rate','desc')
        ->with(['photo','city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($q) {
                $q->select('id','name');
            }]);
        }]) 
        ->take(6)
        ->get();

       $topRated = $topRated->makeHidden(['email','phone_number',
       'details','website_url','created_at','updated_at']);

       $Chain = Hotel::where('type_id','=',1)
        ->take(5)
        ->with(['photo','city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($q) {
                $q->select('id','name');
            }]);
        }]) 
        ->get();
        $Chain = $Chain->makeHidden(['email','phone_number',
       'details','website_url','created_at','updated_at']);

       $Motel = Hotel::where('type_id','=',2)
        ->take(5)
        ->with(['photo','city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($q) {
                $q->select('id','name');
            }]);
        }]) 
        ->get();
        $Motel = $Motel->makeHidden(['email','phone_number',
       'details','website_url','created_at','updated_at']);

       $Resorts = Hotel::where('type_id','=',3)
        ->take(5)
        ->with(['photo','city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($q) {
                $q->select('id','name');
            }]);
        }]) 
        ->get();
        $Resorts = $Resorts->makeHidden(['email','phone_number',
       'details','website_url','created_at','updated_at']);

       $Inns = Hotel::where('type_id','=',4)
       ->take(5)
       ->with(['photo','city'=> function ($query) {
        $query->select('id','name','country_id')
        ->with(['country' => function ($q) {
            $q->select('id','name');
           }]);
        }]) 
       ->get();
       $Inns = $Inns->makeHidden(['email','phone_number',
      'details','website_url','created_at','updated_at']);

      $All_suites = Hotel::where('type_id','=',5)
      ->take(5)
      ->with(['photo','city'=> function ($query) {
        $query->select('id','name','country_id')
        ->with(['country' => function ($q) {
            $q->select('id','name');
           }]);
        }]) 
      ->get();
      $All_suites = $All_suites->makeHidden(['email','phone_number',
     'details','website_url','created_at','updated_at']);



       return response()->json([
            'message'=>"done",
            'topRated'=> $topRated,
            'Chain'=> $Chain,
            'Motel'=> $Motel,
            'Resorts'=> $Resorts,
            'Inns'=> $Inns,
            'All_suites'=> $All_suites,
           ]);
    }

    public function TopRated()
    {
        $topRated = Hotel::orderBy('rate','desc')
        ->with(['photo','city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($q) {
                $q->select('id','name');
            }]);
        }]) 
        ->take(6)
        ->get();

       $topRated = $topRated->makeHidden(['email','phone_number',
       'details','website_url','created_at','updated_at']);

       return response()->json([
        'message'=>"done",
        'topRated'=> $topRated,
       ]);

    }

    public function ShowHotelRooms(Request $request) //done
    {
        $hotel_id = $request->hotel_id;

        $rooms = Room::select('rooms.*', 'hotels.location','hotels.city_id')
        ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
        ->where('rooms.hotel_id', '=', $hotel_id)
        ->with(['photo','features'])
        ->get();

        $rooms = $rooms->makeHidden(['details','created_at','updated_at']);

        return response()->json([
        'message'=>"done",
        'Room:'=>$rooms
        ]);
    }
}
