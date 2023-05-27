<?php

namespace App\Http\Controllers;
use App\Models\Hotel;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    
    public function HoltelLogin(Request $request)
    {
        $request->validate([

            'email'=>'required|email',
            'password'=>'required',
        ]);
        $credentials = request(['email','password']);

        if(auth()->guard('hotel')->attempt($request->only('email','password'))){
            config(['auth.guards.api.provider'=>'hotel']);

            $user = Hotel::query()->select('hotels.*')->find(auth()->guard('hotel')->user()['id']);
            $success=$user;
            $success['token']=$user->createtoken('MyApp',['user'])->accessToken;
            return response()->json($success);
    
        }
        else{
            return response()->json(['error'=>['unauthorized']],401);
        }
    }

    
}
