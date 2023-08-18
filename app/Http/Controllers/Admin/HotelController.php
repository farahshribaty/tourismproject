<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Hotel\AdminController;
use App\Models\Facilities;
use App\Models\Features;
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
    public function getHotelWithAllInfo2(Request $request)
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
    public function deleteAdmin(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:hotel_admins',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        HotelAdmin::where('id','=',$request->id)->delete();

        return $this->success(null,'Admin deleted successfully with his company');
    }
    public function deleteHotel(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:hotels',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }
        
        Hotel::where('id','=',$request->id)->delete();

        return $this->success(null,'Hotel deleted successfully');
    }
    public function editHotelDetails(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:hotels',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $hotel = Hotel::findOrFail($request->id);
        $hotel->fill($request->all());
        $hotel->save();

        return $this->success(null, 'Company edited successfully');
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
    public function addFacilitiesForHotel(Request $request) //add new facility
    {
        $request->validate([
            'name'=>'required'
        ]);
        $facility = new Facilities();
        $facility->name = $request->name;
        $facility->save();

        return $this->success($facility, 'Facility Added successfully');
    }
    public function getAllFacilitiesForHotel() //see all facilities
    {
        $facilities=Facilities::get();
        return $this->success($facilities, 'Facility retrived successfully');

    }
    public function AddFeature(Request $request) //add new feature
    {
        $request->validate([
            'name'=>'required'
        ]);
        $features = new Features();
        $features->name = $request->name;
        $features->save();

        return $this->success($features, 'Feature Added successfully');
    }
    public function getAllFeatures() //see all features
    {
        $features=Features::get();
        return $this->success($features, 'Feature retrived successfully');

    }
    public function DeleteFacility(Request $request)
    {
        Facilities::where('id','=',$request->id)->delete();

        return $this->success(null,'Facility deleted successfully');
    }
    public function DeleteFeature(Request $request)
    {
        Features::where('id','=',$request->id)->delete();

        return $this->success(null,'Feature deleted successfully');
    }


}
