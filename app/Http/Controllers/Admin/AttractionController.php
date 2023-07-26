<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    // This controller contains all operations the main admin can do on the Attraction section.

    public function getAllAttractions()
    {
        $attractions = Attraction::paginate(10);
        return response()->json([
            'success'=>true,
            'data'=>$attractions,
        ]);
    }
}
