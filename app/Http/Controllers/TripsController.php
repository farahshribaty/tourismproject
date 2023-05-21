<?php

namespace App\Http\Controllers;

use App\Models\TripCompany;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    //
    public function register(Request $request)
    {
        TripCompany::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'phone_number'=>$request->phone_number,
            'country_id'=>$request->country_id,
        ]);

        $tripc = TripCompany::where('email','=',$request->email)->first();
        $tripc['token'] = $tripc->createToken('MyApp')->accessToken;
        return response()->json([
            'date'=>$tripc,
        ]);
    }

    public function dashboard(Request $request)
    {
        return $request->user();
    }
}
