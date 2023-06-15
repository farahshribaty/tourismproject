<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\AttractionAdmin;
use App\Models\AttractionPhoto;
use Illuminate\Http\Request;

class AttractionAdminController extends Controller
{
    //

    public function register(Request $request)
    {
        AttractionAdmin::create([
            'email'=>$request->email,
            'password'=>$request->password,
            'attraction_id'=>2,
        ]);

        $attraction = AttractionAdmin::where('email','=',$request->email)->first();
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
