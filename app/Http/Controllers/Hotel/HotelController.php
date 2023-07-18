<?php

namespace App\Http\Controllers\Hotel;


use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelReview;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

//        $data = Room::select(['cities.id as city_id',DB::raw('SUM(rooms.id) as room_id_sum')])
//        ->join('hotels','rooms.hotel_id','=','hotels.id')
//        ->join('room_types','room_types.id','=','rooms.room_type')
//        ->join('cities','hotels.city_id','=','cities.id')
//        ->join('countries','countries.id','=','cities.country_id')
//        ->join('room_photos','room_photos.room_id','=','rooms.id')
//            ->groupBy(['cities.id'])->get();
//
//
//        $data = Order::select(['orders.order_data',DB::raw('SUM(order_products.quantity) as all_quantity')])
//            ->join('order_lists','orders.OrderList_id','=','order_lists.id')
//            ->join('order_products','order_lists.id','=','order_products.OrderList_id')
//            ->groupBy('orders.order_date')->get();
//
//        return $data;

        $topRated = Room::withAllInformation()
            ->orderBy('hotels.rate','desc')
            ->take(6)
            ->get();


        $NonSmokingroom = Room::withAllInformation()
            ->where('room_types.name','=','Non-Smoking room')
            ->take(6)
            ->get();


        $Accessibleroom = Room::withAllInformation()
            ->where('room_types.name','=','Accessible room')
            ->take(6)
            ->get();


        $Singlerooms = Room::withAllInformation()
            ->where('room_types.name','=','Single rooms')
            ->take(6)
            ->get();


        $suiet = Room::withAllInformation()
            ->where('room_types.name','=','Suiet')
            ->take(6)
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
