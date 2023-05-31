<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\controller;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;

class HotelController extends Controller
{

    public function HoltelLogin(Request $request)
    {
//        $request->validate([
//
//            'email'=>'required|email',
//            'password'=>'required',
//        ]);
//        $credentials = request(['email','password']);
//
//        if(auth()->guard('hotel')->attempt($request->only('email','password'))){
//            //config(['auth.guards.api.provider'=>'hotel']);
//
//            $user = Hotel::query()->select('hotels.*')->find(auth()->guard('hotel')->user()['id']);
//            $success=$user;
//            $success['token']=$user->createtoken('MyApp',['user'])->accessToken;
//            return response()->json($success);
//
//        }
//        else{
//            return response()->json(['error'=>['unauthorized']],401);
//        }


        $hotel = Hotel::where('email',$request->email)->first();
        if(!$hotel){
            return 'not found';
        }

        if($hotel['password']==$request->password){
            Auth::login($hotel);
            $hotel['token'] = $hotel->createToken('MyApp')->accessToken;
            return $hotel;
        }

        return 'password does not match';
    }


}
