<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\Types;
use App\Models\Features;
use App\Models\RoomType;
use App\Models\HotelAdmin;
use App\Models\HotelPhoto;
use App\Models\RoomPhotos;
use Illuminate\Http\Request;
use App\Models\RoomFeatures;
use App\Models\HotelUpdating;
use Illuminate\Support\Carbon;
use App\Models\HotelsFacilities;
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
            'stars',
            'price_start_from',
            'website_url',
            'city_id',
            'type_id',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $data = $request;
        $data['hotel_admins_id'] = $request->user()->id;
        $data['admin_id'] = $request->user()->id;
        $data['add_or_update'] = 0;
        $data['accepted'] = 0;
        $data['rejected'] = 0;
        $data['seen'] = 0;
        $data['num_of_rooms'] = 0;
        $data['num_of_ratings'] = 0;
        $data['rate'] = 0;

        HotelUpdating::create($data->all());
        return $this->success(null,'Form sent successfully, pending approval.');
    }
    // public function CreateHotel(Request $request)
    // {
    //     $add_request = HotelUpdating::where('hotel_admins_id', $request->user()->id)
    //         ->where('rejected', 0)->first();

    //     if ($add_request) {
    //         return $this->error('You no longer have the ability to add your company');
    //     }

    //     $selectedFacilities = $request->selectedFacilities;

    //     if (!is_array($selectedFacilities)) {
    //         return response()->json([
    //             'error' => 'Selected features must be provided as an array.'
    //         ], 400);
    //     }

    //     $validated_data = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required|email',
    //         'location' => 'required',
    //         'phone_number' => 'required',
    //         'details' => 'required',
    //         'stars' => 'required|integer',
    //         'price_start_from' => 'required|numeric',
    //         'website_url' => 'required|url',
    //         'city_id' => 'required|integer',
    //         'type_id' => 'required|integer',
    //     ]);

    //     if ($validated_data->fails()) {
    //         return response()->json(['error' => $validated_data->errors()->all()]);
    //     }

    //     $data = $validated_data->validated();
    //     $data['hotel_admins_id'] = $request->user()->id;
    //     $data['admin_id'] = $request->user()->id;
    //     $data['add_or_update'] = 0;
    //     $data['accepted'] = 0;
    //     $data['rejected'] = 0;
    //     $data['seen'] = 0;
    //     $data['num_of_rooms'] = 0;
    //     $data['num_of_ratings'] = 0;
    //     $data['rate'] = 0;

    //     $newHotel = HotelUpdating::create($data);

    //     $facilityRecords = [];
    //     $now = Carbon::now();

    //     foreach ($selectedFacilities as $facilityId) {
    //         $facilityRecords[] = [
    //             'hotel_id' => $newHotel->id,
    //             'facilities_id' => $facilityId,
    //             'created_at' => $now,
    //             'updated_at' => $now
    //         ];
    //     }

    //     HotelsFacilities::insert($facilityRecords);

    //     return $this->success(null, 'Form sent successfully, pending approval.');
    // }
    /**
     * Getting one hotel with all info
     */
    public function getHotelWithAllInfoByToken(Request $request)
    {
      $hotel = Hotel::where('admin_id',$request->user()->id)->first();
      if (!isset($hotel)) {
        return response()->json([
            'data'=>null,
            'message'=>"hotel not found",
        ],200);
     }
     $request["id"]=$hotel->id;
     return $this->getHotelWithAllInfo($request);
    }
    public function getHotelWithAllInfo(Request $request)
    {
        $hotel = Hotel::with(['type', 'city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($que) {
                $que->select('id','name'); }]);
                } , 'photo', 'facilities','reviews'=> function($qu){
                $qu->select('id','hotel_id','rate','comment','user_id')
                ->with(['user' => function ($q) {
                $q->select('id','first_name','last_name');
                }]);
            }])
        ->where('id',$request->id)
        ->get();

        $roomsByType = DB::table('rooms')
        ->join('room_types', 'rooms.room_type', '=', 'room_types.id')
        ->select('name as room_type','rooms.beds', DB::raw('COUNT(*) as room_count'))
        ->groupBy('room_types.name','rooms.beds')
        ->where('hotel_id', $request->id)
        ->get();

        return response([
            'Hotel_info'=>$hotel,
            'Rooms'=>$roomsByType
        ]);
    }
    public function addFacilitisForHotel(Request $request)
    {
        $hotel = Hotel::where('admin_id', '=', $request->user()->id)->first();
        $selectedFacilities = $request->selectedFacilities;

        if (!is_array($selectedFacilities)) {
            return response()->json([
                'error' => 'Selected features must be provided as an array.'
            ], 400);
        }
        $facilityRecords = [];
        $now = Carbon::now();

        foreach ($selectedFacilities as $facilityId) {
            $facilityRecords[] = [
                'hotel_id' => $hotel->id,
                'facilities_id' => $facilityId,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        HotelsFacilities::insert($facilityRecords);
        return $this->success($facilityRecords,'Facilities For This Hotel added successfuly');

    }
    public function addOneFacility(Request $request)
    {
        $hotel = Hotel::where('admin_id', '=', $request->user()->id)->first();
        $selectedFacility = $request->selectedFacility;

        // Check if the selected facility already exists for the hotel
        $existingFacility = HotelsFacilities::where('hotel_id', '=', $hotel->id)
            ->where('facilities_id', '=', $selectedFacility)
            ->exists();

        if ($existingFacility) {
            return $this->error('The facility has already been added to the hotel.');
        }

        // Add the selected facility to the hotel
        HotelsFacilities::create([
            'hotel_id' => $hotel->id,
            'facilities_id' => $selectedFacility,
        ]);

        // Get all facilities for the hotel
        $data = HotelsFacilities::where('hotel_id', '=', $hotel->id)->get();

        return $this->success($data, 'All Facilities for This Hotel');
    }
    public function getAllFacilitiesForThisHotel(Request $request)
    {
        $hotel = Hotel::where('admin_id', '=', $request->user()->id)->first();
        $data = HotelsFacilities::where('hotel_id','=',$hotel->id)->get();
        return $this->success($data,'All Facilities For This Hotel');
    }
    public function deleteFacility(Request $request)
    {
        $hotel = Hotel::where('admin_id', '=', $request->user()->id)->first();
        $selectedFacility = $request->selectedFacility;

        // Check if the facility exists for the hotel
        $existingFacility = HotelsFacilities::where('hotel_id', '=', $hotel->id)
            ->where('facilities_id', '=', $selectedFacility)
            ->first();

        if (!$existingFacility) {
            return $this->error('The facility does not exist for the hotel.');
        }

        // Delete the facility from the hotel
        $existingFacility->delete();

        return $this->success(null, 'Facility deleted successfully.');
    }
    /***
     * Creating rooms from the same type
     */
    public function addMultiRoomsByType(Request $request)
    {
        $selectedFeatures = $request->selectedFeatures;

        if (!is_array($selectedFeatures)) {
            return response()->json([
                'error' => 'Selected features must be provided as an array.'
            ], 400);
        }

        $rooms = [];

        for ($i = 0; $i < $request->n; $i++) {
            $room = Room::create([
                'room_type' => $request->room_type,
                'hotel_id' => $request->hotel_id,
                'details' => $request->details,
                'price_for_night' => $request->price_for_night,
                'rate' => 0,
                'num_of_ratings' => 0,
                'Sleeps' => $request->Sleeps,
                'Beds' => $request->Beds
            ]);

            $rooms[] = $room;
        }

        $featureRecords = [];

        $now = Carbon::now();

        foreach ($selectedFeatures as $featureId) {
            foreach ($rooms as $room) {
                $featureRecords[] = [
                    'room_id' => $room->id,
                    'features_id' => $featureId,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        RoomFeatures::insert($featureRecords);

        return response()->json([
            'message' => 'Rooms created successfully.',
            'data' => $rooms,
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
        'Room'=>$rooms
        ]);
    }
    public function DeleteRoom(Request $request)
    {
        $room_id = $request->room_id;
        $room = Room::find($room_id);

        if (!$room) {
            return response()->json([
                'error' => 'Room not found.'
            ], 404);

        }

        $room->Reservations()->delete();

        $room->photo()->delete();
        // Remove associated records from the pivot table
        $room->features()->detach();

        // Now delete the room
        $room->delete();

        return $this->success(null, 'Room deleted successfully.');
    }
    public function deleteFeatureFromRoom(Request $request)
    {
        $room = Room::find($request->roomId);

        if (!$room) {
            return response()->json([
                'error' => 'Room not found.'
            ], 404);
        }

        $room->features()->detach($request->featureId);

        return $this->success(null, 'Facility deleted successfully.');
    }


    //unused functions:
    public function getHotelType(Request $request)
    {
        $types = Types::get();
        return response()->json([
            'data'=>$types,
            'success'=>true,
        ], 200);
    }
    public function getRoomType(Request $request)
    {
        $types = RoomType::get();
        return response()->json([
            'data'=>$types,
            'success'=>true,
        ], 200);
    }
    public function getRoomFeatures(Request $request)
    {
        $types = Features::get();
        return response()->json([
            'data'=>$types,
            'success'=>true,
        ], 200);
    }

}
