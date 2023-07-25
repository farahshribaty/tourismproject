<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use Illuminate\Http\Request;
use Nette\Utils\DateTime;


class AdminAttractionController extends Controller
{
    //
    public function addAttraction(Request $request)
    {

        $request->validate([
            'open_at'=>'required',
            'close_at'=>'required',
            'email'=>'required|unique:attractions',
        ]);

        $att = Attraction::create([
            'city_id'=>1,
            'attraction_type_id'=>1,
            'name'=>'hello',
            'email'=>$request->email,
            'password'=>'helloh',
            'location'=>'damascus',
            'phone_number'=>324354,
            'rate'=>3,
            'num_of_ratings'=>23,
            'open_at'=> $request->open_at,
            'close_at'=> $request->open_at,
            'available_days'=>1010101,
            'child_ability_per_day'=>34,
            'adult_ability_per_day'=>34,
            'details'=>'hello',
            'website_url'=>'ejlksjf',
            'adult_price'=>354,
            'child_price'=>343,
            'points_added_when_booking'=>43,
        ]);

        $date = $att['open_at'];

        $new_date = DateTime::createfromformat('Y-m-d H:i:s',$date);
        $att['open_at'] = $new_date->format('H:i');

     //      $att['open_at']=$att['open_at']->format('H:i:s');

        return response()->json([
            $att,
        ]);
    }
}
