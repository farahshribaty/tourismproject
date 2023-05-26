<?php

namespace App\Http\Controllers;
use App\Models\Hotel;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function Hotelregister(Request $request)
    {
        $request->validate([
            'name'=>['required','max:55'],
            'email'=>['email','required','unique:hotels'],
            'password'=>[
                'required',
               'confirmed',
               password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
             ]
            ]);     
        
            
            $hotel = new Hotel();
            $hotel->name = $request->name;
            $hotel->email = $request->email;
            $hotel->password = bcrypt($request->password);
            
            $hotel->save();

          $accessToken=$hotel->createtoken('MyApp',['hotel'])->accessToken;
    
          return response()->json([
                   'hotel'=> $hotel,
                   'access_token'=>$accessToken
            ]);
    }

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
