<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function success(mixed $data, string $message = "okay", int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data'=>$data,
            'success'=>true,
            'message'=>$message,
        ], $statusCode);
    }

    public function error(string $message,int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'data'=>null,
            'success'=>false,
            'message'=>$message,
        ],$statusCode);
    }

    public function dayNumber($day)
    {
//        if($day == 'Saturday') return 0;
//        if($day == 'Sunday') return 1;
//        if($day == 'Monday') return 2;
//        if($day == 'Tuesday') return 3;
//        if($day == 'Wednesday') return 4;
//        if($day == 'Thursday') return 5;
//        if($day == 'Friday') return 6;

        $week = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
        for($i = 0 ; $i < 7 ; $i++){
            if($week[$i]==$day) return $i;
        }
    }


}
