<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripCompany;
use Illuminate\Http\Request;

class TripController extends Controller
{
    // This controller contains all operations the main admin can do on the Trip section.

    public function getAllTripCompanies()
    {
        $companies = TripCompany::paginate(10);
        return response()->json([
            'success'=>true,
            'data'=>$companies,
        ]);
    }
}
