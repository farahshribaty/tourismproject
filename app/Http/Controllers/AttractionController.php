<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    //

    public function register(Request $request)
    {
        Attraction::create([
            'city_id'=>$request->city_id,
            'attraction_type_id'=>$request->attraction_type_id,
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'location'=>$request->location,
            'phone_number'=>$request->phone_number,
            'open_at'=>$request->open_at,
            'close_at'=>$request->close_at,
            'available_days'=>$request->available_days,
            'website_url'=>$request->website_url,
        ]);

        $attraction = Attraction::where('email','=',$request->email)->first();
        $attraction['token'] = $attraction->createToken('MyApp')->accessToken;
        return response()->json([
            'data'=>$attraction,
        ]);
    }

    public function dashboard(Request $request)
    {
        return $request->user();
    }
}
