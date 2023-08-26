<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\AirlineAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FlightController extends Controller
{
    // This controller contains all operations the main admin can do on the Flight section.
    public function getAllAdmins()
    {
        $admins = AirlineAdmin::with([
            'airline'=>function($q){
                $q->select('id','name','admin_id');
            }
        ])->paginate(10);
        return $this->success($admins,'Admins retrieved successfully');
    }


    public function deleteAdmin(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:airline_admins',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        AirlineAdmin::where('id','=',$request->id)->delete();

        return $this->success(null,'Admin deleted successfully with his company');
    }
    public function getAllAirlinesWithMainInfo()
    {
        $airline = Airline::with('Admin')->paginate(10);
        return $this->success($airline, 'Retrieved successfully');
    }
    public function getAirlineWithAllInfo(Request $request)
    {
        $airline = Airline::where('airlines.id','=',$request->id)
        ->with('Admin','country','flights')->get();
        return $this->success($airline, 'Retrieved successfully');
    }
}

