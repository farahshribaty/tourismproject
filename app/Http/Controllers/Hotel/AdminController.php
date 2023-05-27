<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function CreateHotel(Request $request)
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

}
