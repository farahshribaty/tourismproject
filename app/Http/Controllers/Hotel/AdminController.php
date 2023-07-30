<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\HotelPhoto;
use App\Models\Room;
use App\Models\RoomPhotos;
use App\Models\Types;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{

    public function AdminLogin(Request $request) //Hotel admin log in
    {
        $request->validate([

            'email'=>'required|email',
            'password'=>'required',
        ]);
        $admin=Admin::where('email',$request->email)->first();

        if(!$admin)
        {
            return 'user not found';
        }
        if($admin['password']==$request->password)
        {
            Auth::login($admin);
            $hotel['token']=$admin->createtoken('MyApp')->accessToken;
            return response([
                'message'=>'admin loged in',
                'admin'=>$admin
            ]);

        }
        else
        {
            return 'password not found';
        }
        
    }
    public function CreateHotel(Request $request) //done
    {
        Hotel::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'location'=>$request->location,
            'phone_number'=>$request->phone_number,
            'details'=>$request->details,
            'rate'=>$request->rate ,
            'num_of_ratings'=> $request->num_of_ratings,
            'website_url'=>$request->website_url,
            'city_id'=>$request->city_id,
            'type_id'=>$request->type_id
        ]);

        $hotel = Hotel::where('email','=',$request->email)->first();
        $hotel['token'] = $hotel->createToken('MyApp')->accessToken;
        
        return response()->json([
            'data'=>$hotel,
        ]);

    }
    public function addPhotos(Request $request) //done
    {
        if($request->hasFile('photo')) {
            $file_extension = $request->photo->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extension;
            $path = 'images/hotel';
            $request->photo->move($path, $file_name);
            HotelPhoto::create([
                'path'=> 'http://127.0.0.1:8000/images/hotel/'.$file_name,
                'hotel_id'=>$request->hotel_id,
            ]);
        
         return response()->json([
            'status'=>true,
            'message'=>'photo added successfully',
         ]);

        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'its not a photo file',
             ]);
        }
    }
    public function addRooms(Request $request) //done
    {
        $room=Room::create([
            'room_type'=>$request->room_type,
            'hotel_id'=>$request->hotel_id,
            'details'=>$request->details,
            'price_for_night'=>$request->price_for_night,
            'rate'=>$request->rate,
            'num_of_ratings'=>$request->rate
        ]);
        
        return response()->json([
            'data'=>$room,
        ]);
    }
    public function addRoomPhotos(Request $request) //done
    {
        if($request->hasFile('photo')) {
            $file_extension = $request->photo->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extension;
            $path = 'images/room';
            $request->photo->move($path, $file_name);
            RoomPhotos::create([
                'path'=> 'http://127.0.0.1:8000/images/room/'.$file_name,
                'room_id'=>$request->room_id,
            ]);
        
         return response()->json([
            'status'=>true,
            'message'=>'photo added successfully',
         ]);

        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'its not a photo file',
             ]);
        }
    }
    public function SeeAllRooms(Request $request)
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
    public function DeleteRoom(Request $request)
    {
        $room_id = $request->room_id;
        $room = Room::find($room_id);
        if(!isset($photo)){
            return $this->error('Photo not found');
        }
        $room->delete();
        return response()->json([
        'message' => 'Room deleted successfully'], 200);
    }
    
}
