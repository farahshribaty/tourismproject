<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\HotelAdmin;
use App\Models\HotelPhoto;
use App\Models\HotelUpdating;
use App\Models\Room;
use App\Models\RoomPhotos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    public function AdminLogin(Request $request) 
    {
        $request->validate([

            'user_name'=>'required',
            'password'=>'required',
        ]);
        $admin=HotelAdmin::where('user_name',$request->user_name)->first();

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
                'admin'=>$admin,
                'token:'=>$hotel['token']
            ]);

        }
        else
        {
            return 'password not found';
        }
        
    }
    /**
     * Adding My Hotel info
     */
    public function CreateHotel(Request $request)
    {
        $add_request = HotelUpdating::where('hotel_admins_id',$request->user()->id)
        ->where('rejected',0)->first();

         if(isset($add_request)){
        return $this->error('You no longer have the ability to add your company');
        }
        $validated_data = Validator::make($request->all(), [
            'name',
            'email',
            'location',
            'phone_number',
            'details',
            'num_of_rooms',
            'rate',
            'stars',
            'num_of_ratings',
            'price_start_from',
            'website_url',
            'city_id',
            'type_id',
            // 'admin_id'
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }
    
        $data = $request;
        $data['hotel_admins_id'] = $request->user()->id;
        $data['add_or_update'] = 0;
        $data['accepted'] = 0;
        $data['rejected'] = 0;
        $data['seen'] = 0;
    
        HotelUpdating::create($data->all());
        return $this->success(null,'Form sent successfully, pending approval.');
    }   
    /**
     * Getting all hotels with main info
     */
    public function getAllHotelsWithMainInfo()
    {
        $hotels = DB::table('hotels')->select('id', 'name','email', 'location','phone_number','details')->get();

        return $hotels;
    }
    /**
     * Getting one hotel with all info
     */
    public function getHotelWithAllInfo(Request $request)
    {
        $hotel = DB::table('hotels')->where('id', $request->id)->get();

        return $hotel;
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
        ->with(['photo','features','type'])
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
