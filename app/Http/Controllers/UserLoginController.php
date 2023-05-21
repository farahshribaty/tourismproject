<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    public function userLogin(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|email',
//            'password' => 'required',
//        ]);

//        if($validator->fails()){
//
//            return response()->json(['error' => $validator->errors()->all()]);
//        }

        if(Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])){

            config(['auth.guards.api.provider' => 'user']);

            $token = Auth::guard('user')->user()->createToken('MyApp',['user'])->accessToken;

            return Auth::guard('user')->user();
            return response()->json(['token' => $token], 200);

        }else{

            return response()->json(['error' => ['Email and Password are Wrong.']], 200);
        }
    }

    public function userDashboard(Request $request)
    {
        return response()->json(
            $request->user()
        );
        return response()->json(Auth::guard('user')->user());
    }
}
