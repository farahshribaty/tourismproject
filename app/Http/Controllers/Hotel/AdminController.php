<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Types;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{

    public function Login() //Hotel admin log in
    {
      //
    }

    public function HoltelLogin(Request $request) //mo zapt
    {
        $request->validate([

            'email'=>'required|email',
            'password'=>'required',
        ]);
        
        $hotel=Hotel::where('email',$request->email)->first();

        if(!$hotel)
        {
            return 'user not found';
        }
        if($hotel['password']==$request->password)
        {
            Auth::login($hotel);
            $hotel['token']=$hotel->createtoken('MyApp')->accessToken;
            return response([
                'message'=>'hotel added',
                'hotel'=>$hotel
            ]);

        }
        else
        {
            return 'password not found';
        }
        
    }
    
    public function CreateHotel(Request $request,$id) //hotel type id
    {
        $type_id=DB::table('types')
            ->where('types.id','=',$id)
            ->get();

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
            //$hotel_type=Types::find($id);
            
            $hotel = new Hotel();
            $hotel->name = $request->name;
            $hotel->email = $request->email;
            $hotel->password = bcrypt($request->password);
            $hotel->type_id=$type_id[0]->id;
            $hotel->save();

          $accessToken=$hotel->createtoken('MyApp',['hotel'])->accessToken;
    
          return response()->json([
                   'hotel'=> $hotel,
                   'access_token'=>$accessToken
            ]);
    }

}
