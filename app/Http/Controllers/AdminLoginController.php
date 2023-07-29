<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
//    public function adminLogin(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|email',
//            'password' => 'required',
//        ]);
//
//        if($validator->fails()){
//
//            return response()->json(['error' => $validator->errors()->all()]);
//        }
//
//        if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])){
//
//            config(['auth.guards.api.provider' => 'admin']);
//
////            $valu = Auth::guard('admin')->user();
////            return $valu;
//            //$token = Auth::guard('admin')->user()->createToken('MyApp',['admin'])->accessToken;
//
//            return response()->json(['token' => $token], 200);
//
//        }else{
//            return response()->json(['error' => ['Email and Password are Wrong.']], 200);
//        }
//    }

    public function CreateHotel()
    {

    }

    public function adminDashboard()
    {
        return response()->json(Auth::guard('admin')->user());
    }
}
