<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;

class DoctorLoginController extends Controller
{
    public function doctorLogin(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|email',
//            'password' => 'required',
//        ]);
//
//        if($validator->fails()){
//
//            return response()->json(['error' => $validator->errors()->all()]);
//        }

//        if(Auth::guard('doctor')->attempt(['email' => $request->email, 'password' => $request->password])){
//
//            config(['auth.guards.api.provider' => 'doctor']);
//
//            $token = Auth::guard('doctor')->user()->createToken('MyApp',['doctor'])->accessToken;
//
//            return response()->json(['token' => $token], 200);
//
//        }else{
//
//            return response()->json(['error' => ['Email and Password are Wrong.']], 200);
//        }
        $doctor = Doctor::where('email','=',$request->email)->first();
        $token = $doctor->createToken('MyApp')->accessToken;
        return $request->user();
    }

    public function doctorDashboard(Request $request)
    {
        return $request->user();
    }
}
