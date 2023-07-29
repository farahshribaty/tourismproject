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
        $week = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
        for($i = 0 ; $i < 7 ; $i++){
            if($week[$i] == $day) return $i;
            if($i == $day) return $week[$i];
        }
    }

    public function convertWeekArrayToBitmask($week)
    {
        $bit = 0;
        foreach($week as $key=>$value){
            if($value){
                $bit = $bit|(1<<($this->dayNumber($key)));
            }
        }
        return $bit;
    }

    public function convertBitmaskToWeekArray($bitmask)
    {
        $week = [];
        for($i=0 ; $i<7 ; $i++){
            if($bitmask&(1<<$i)){
                $week[$this->dayNumber($i)] = 1;
            }
            $week[$this->dayNumber($i)] = (($bitmask&(1<<$i))>0);
        }
        return $week;
    }


}
