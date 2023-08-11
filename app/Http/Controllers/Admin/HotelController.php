<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Hotel\AdminController;
use App\Models\Facilities;
use App\Models\Hotel;
use App\Models\HotelAdmin;
use App\Models\HotelUpdating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HotelController extends AdminController
{
    // This controller contains all operations the main admin can do on the Hotel section.

    public function makeNewAdmin(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'first_name',
            'last_name',
            'user_name' => 'required|unique:hotel_admins',
            'email'=>'required|max:60',
            'password' => 'required',
            'phone_number' => 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        HotelAdmin::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'user_name'=>$request->user_name,
            'email'=>$request->email,
            'password'=>$request->password,
            'phone_number'=> $request->phone_number,
        ]);

        $hotel = HotelAdmin::where('user_name','=',$request->user_name)->first();
        $hotel['token'] = $hotel->createToken('MyApp')->accessToken;

        return $this->success($hotel,'Admin added successfully');
    }
    public function getAllHotelsWithMainInfo()
    {
        $hotel = Hotel::with('admin')->paginate(10);
        return $this->success($hotel, 'Retrieved successfully');
    }
<<<<<<< HEAD
    public function getHotelWithAllInfo2(Request $request)
=======

    public function getHotelWithAllInfo2($id)
>>>>>>> d0a43aa64dbbf2a01bf5780d9966d87f4fc1090d
    {
        $hotel = $this->getHotelWithAllInfo($request);
        return $hotel;
    }
    public function acceptingHotel(Request $request)  // both adding or updating an Hotel
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:hotel_updatings',
            'accepted' => 'required',
            'rejected' => 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $updates = HotelUpdating::where('id','=',$request->id)->first()->toArray();

        if($updates['accepted']==1 || $updates['rejected']==1){
            return $this->error('You can not accept/reject this update twice');
        }

        if($request->accepted == $request->rejected){
            return $this->error('Select one: accept or reject');
        }


        if($request->rejected == 1){            // if the updates/adds rejected
            HotelUpdating::where('id','=',$request->id)->update([
                'rejected'=> 1,
            ]);

            return $this->success(null,'Updates rejected successfully');
        }


        if(!isset($updates)){
            return $this->error('Updates not found');
        }

        HotelUpdating::where('id','=',$request->id)->update(['accepted'=>1]);

        if($updates['add_or_update']){       // updating an Hotel info
            foreach($updates as $key=>$value){
                if($value == null) unset($updates[$key]);
            }
            $hotel = Hotel::findOrFail($updates['hotel_id']);
            $hotel->fill($updates);
            $hotel->save();
            return response([
                'hotel'=>$hotel,
            ]);

            return $this->success(null,'Updates accepted successfully');
        }

        else{       // adding new Hotel
            $updates['rate'] = 0;
            $updates['num_of_ratings'] = 0;
            Hotel::create($updates);
            return response([
                'hotel'=>$updates,
            ]);
            return $this->success(null,'Hotel accepted successfully');
        }
    }
    public function getAllHotelAdmins()
    {
        $admins = HotelAdmin::with([
            'Hotel'=>function($q){
                $q->select('id','name','admin_id');
            }
        ])->paginate(10);
        return $this->success($admins,'Admins retrieved successfully');
    }
    public function getUpdatingDetails(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:hotel_updatings',
        ]);
        if ($validated_data->fails()) {
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $update = HotelUpdating::where('id', '=', $request->id)
            ->with('admin')
            ->first();

        HotelUpdating::where('id', '=', $request->id)->update([
            'seen' => 1,
        ]);

        return $this->success($update, 'Update retrieved successfully');
    }
    public function addFacilitiesForHotel(Request $request)
    {
        $request->validate([
            'name'=>'required'
        ]);
        $facility = new Facilities();
        $facility->name = $request->name;
        $facility->save();

        return $this->success($facility, 'Facility Added successfully');
    }
    public function getAllFacilitiesForHotel()
    {
        $facilities=Facilities::get();
        return $this->success($facilities, 'Facility Added successfully');

    }



}
