<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\AttractionPhoto;
use Illuminate\Http\Request;

class AttractionAdminController extends Controller
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
            'details'=>$request->details,
            'rate'=> 0,
            'num_of_ratings'=> 0,
            'adult_price'=>$request->adult_price,
            'child_price'=>$request->child_price,
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

    public function addPhotos(Request $request)
    {
        if($request->hasFile('photo')) {
            $file_extension = $request->photo->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extension;
            $path = 'images/attraction';
            $request->photo->move($path, $file_name);
            AttractionPhoto::create([
                'path'=> 'http://127.0.0.1:8000/images/attraction/'.$file_name,
                'attraction_id'=>$request->user()->id,
            ]);
        }
        return response()->json([
            'status'=>true,
            'message'=>'photo added successfully',
        ]);

    }
}
