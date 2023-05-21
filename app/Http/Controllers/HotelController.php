<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Doctor;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    //
    public function register(Request $request)
    {

        Hotel::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'phone_number'=>$request->phone_number,
            'location'=>$request->location,
            'city_id'=>$request->city_id,

        ]);
        $hotel = Hotel::where('email','=',$request->email)->first();

        $hotel['token'] = $hotel->createToken('MyApp')->accessToken;
        //$token = $hotel->createToken('MyApp')->accessToken;
        return response()->json($hotel);
//        $doctor = Doctor::where('id','=',1)->first();
//        $doctor['token'] = $doctor->createToken('MyApp')->accessToken;
//        return $doctor;
    }

    public function airRegister(Request $request)
    {

        Airline::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'phone_number'=>$request->phone_number,
            'city_id'=>$request->city_id,
        ]);
        $airline = Airline::where('email','=',$request->email)->first();

        $airline['token'] = $airline->createToken('MyApp')->accessToken;
        //$token = $hotel->createToken('MyApp')->accessToken;
        return response()->json($airline);
//        $doctor = Doctor::where('id','=',1)->first();
//        $doctor['token'] = $doctor->createToken('MyApp')->accessToken;
//        return $doctor;
    }



    public function dashboard(Request $request)
    {
        return response()->json([
            $request->user()
        ]);
    }
}
