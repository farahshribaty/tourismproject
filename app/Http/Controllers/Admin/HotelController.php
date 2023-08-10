<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Hotel\AdminController;
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
    public function CreateAdmin(Request $request)
    {
        $request->validate([
            'first_name'=>['required','max:55'],
            'last_name'=>['required','max:55'],
            'user_name'=>['required','unique:hotels'],
            'email'=>['required','max:60'],
            'password'=>[
                'required',
               password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
            ],
             'phone_number'=>['required']
            ]);

        $admin = new HotelAdmin();
        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->user_name = $request->user_name;
        $admin->email = $request->email;
        $admin->password =$request->password;  //should add bcrypt() but it didn;t eork on the login bcuz of it
        $admin->phone_number = $request->phone_number;

        $admin->save();

        $accessToken=$admin->createtoken('MyApp',['admin'])->accessToken;

          return response()->json([
                   'admin'=> $admin,
                   'access_token'=>$accessToken
            ]);
    }
    public function getAllHotelsWithMainInfo()
    {
        // $hotels = DB::table('hotels')->select('id', 'name','email', 'location','phone_number','details')->get();
        // return $hotels;
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



}
