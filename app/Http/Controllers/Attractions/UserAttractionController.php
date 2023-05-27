<?php

namespace App\Http\Controllers\Attractions;

use App\Http\Controllers\Controller;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Models\Attraction;
use App\Models\AttractionReview;
use Illuminate\Http\Request;

class UserAttractionController extends Controller
{

    /**
     * Main Page For Attractions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $topRated = Attraction::orderBy('rate','desc')
            ->with(['photo','city'])
            ->take(6)
            ->get();
        $topRated = $topRated->makeHidden(['email','location','attraction_type_id','phone_number','open_at','close_at','available_days','details','website_url']);

        $paid = Attraction::orderBy('adult_price','desc')
            ->where('adult_price','>',0)
            ->with(['photo','city'])
            ->take(6)
            ->get();
        $paid = $paid->makeHidden(['email','location','attraction_type_id','phone_number','open_at','close_at','available_days','details','website_url']);

        $free = Attraction::where('adult_price','=',0)
            ->with(['photo','city'])
            ->take(6)
            ->get();
        $free = $free->makeHidden(['email','location','attraction_type_id','phone_number','open_at','close_at','available_days','details','website_url']);


        return response()->json([
            'status'=>true,
            'topRated'=>$topRated,
            'paid'=>$paid,
            'free'=>$free,
        ]);
    }

    /**
     * Search For Attractions With Specific Criteria
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchForAttractions(Request $request)
    {
        $attraction = Attraction::whereHas('city',function($query) use($request){
                $query->where('name',$request->word);
            })
            ->orWhere('name','like','%'.$request->word.'%')
            ->when($request->price,function($query) use($request){
                $query->where('adult_price','<=',$request->price);
            })
            ->when($request->attraction_type_id,function($query) use($request){
                $query->where('attraction_type_id','=',$request->attraction_type_id);
            })
            ->with(['photo','city'])
            ->paginate(10);

        return response()->json([
            'success'=>true,
            'data'=>$attraction,
        ],200);
    }


    public function viewAttractionDetails()
    {

    }

    public function bookTicket()
    {

    }

    public function addReview(Request $request)
    {
        $request->validate([
            'stars'=>'required',
            'attraction_id'=>'required',
        ]);

        $lastRate = AttractionReview::where([
            'user_id'=>$request->user()->id,
            'attraction_id'=>$request->attraction_id,
        ])->first();

        if($lastRate){
            return response()->json([
                'success'=>false,
                'message'=>'you can not rate this attraction more than one time',
            ]);
        }

        $comment = null;
        if(isset($request->comment)){
            $comment = $request->comment;
        }

        AttractionReview::create([
            'stars'=>$request->stars,
            'comment'=>$comment,
            'user_id'=>$request->user()->id,
            'attraction_id'=>$request->attraction_id,
        ]);

        //recalculating the rate of the attraction

        $attraction = Attraction::where('id',$request->attraction_id)->first();
        if(!$attraction){
            return response()->json([
                'success'=>false,
                'message'=>'attraction not found',
            ]);
        }

        $num_of_ratings = $attraction['num_of_ratings'];
        $rate = $attraction->rate;

        $new_rate = (($num_of_ratings*$rate)+$request->stars)/($num_of_ratings+1);

        Attraction::where('id',$request->attraction_id)
            ->update([
            'rate'=> $new_rate,
            'num_of_ratings'=> $num_of_ratings+1,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'review has sent successfully',
        ]);
    }
}
