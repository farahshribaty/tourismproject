<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\City;
use App\Models\HotelReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    public function Register(Request $request)
    {
        $request->validate([
            'first_name'=>['required','max:55'],
            'last_name'=>['required','max:55'],
            'email'=>['email','required'/*,'unique:users'*/],
            'password'=>[
                'required',
               'confirmed',
               password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
            ],
             'phone_number'
            ]);

            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->phone_number = $request->phone_number;
            $user->save();

          $accessToken=$user->createtoken('MyApp',['user'])->accessToken;

          return response()->json([
                   'user'=> $user,
                   'access_token'=>$accessToken
            ]);
    }

    public function Login(Request $request)
    {
        $request->validate([

            'email'=>'required|email',
            'password'=>'required',
        ]);

        if(auth()->guard('user')->attempt($request->only('email','password'))){
            config(['auth.guards.api.provider'=>'user']);
            $user=User::query()->select('users.*')->find(auth()->guard('user')->user()['id']);
            $success=$user;
            $success['token']=$user->createtoken('MyApp',['user'])->accessToken;
            return response()->json($success);
        }
        else{
            return response()->json(['error'=>['unauthorized']],401);
        }
    }

    public function ShowCities(Request $request) //done
    {
        $cities=City::where('cities.country_id','=',$request->id)
        ->get();

         return response()->json([
        'message' => $cities,
        ]);
    }

    public function ShowCities1(Request $request)
    {
        // Find a country by its ID
        $country = Country::find($request->country_id);

        if (!$country) {
            // Handle the case where the country ID does not exist
            return response()->json(['error' => 'Country not found'], 404);
        }

        // Retrieve all cities in the country
        $cities = City::where('country_id', $country->id)->get();

        // Return the list of cities as a JSON response
        return response()->json(['cities' => $cities], 200);
    }


    public function Hotelsearch(Request $request)
    {
        $query = Hotel::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        else if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->has('num_of_rooms')) {
            $query->where('num_of_rooms', '>=', $request->input('num_of_rooms'));
        }

        if ($request->has('check_in') && $request->has('check_out')) {
            $checkIn = $request->input('check_in');
            $checkOut = $request->input('check_out');

            $query->whereDoesntHave('hotel_resevations', function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($query) use ($checkIn, $checkOut) {
                    $query->where('hotel_resevations.check_in', '>=', $checkIn)
                          ->where('hotel_resevations.check_in', '<=', $checkOut);
                })->orWhere(function ($query) use ($checkIn, $checkOut) {
                    $query->where('hotel_resevations.check_out', '>=', $checkIn)
                          ->where('hotel_resevations.check_out', '<=', $checkOut);
                })->orWhere(function ($query) use ($checkIn, $checkOut) {
                    $query->where('hotel_resevations.check_in', '<=', $checkIn)
                          ->where('hotel_resevations.check_out', '>=', $checkOut);
                });
            });
        }
        
        if ($request->has('rate')) {                    //(filter:rate)
            $query->where('rate', '=', $request->input('rate'));
        }

        $hotels = $query
       ->with(['photo','city','city.country','type'])
       ->paginate(10);

        return response()->json([
            'success'=>true,
            'data'=>$hotels,
        ],200);
    }

    public function searchForHotel(Request $request)
    {
        $hotel = Hotel::whereHas('city',function($query) use($request){
                $query->where('name',$request->word);
            })
            ->orWhere('name','like','%'.$request->word.'%')
            ->when($request->price,function($query) use($request){
                $query->where('adult_price','<=',$request->price);
            })
            ->when($request->hotel_type_id,function($query) use($request){
                $query->where('hotel_type_id','=',$request->hotel_type_id);
            })
            ->with(['photo','city'])
            ->paginate(10);

        return response()->json([
            'success'=>true,
            'data'=>$hotel,
        ],200);
    }

    public function addReview(Request $request) //done
    {
        $request->validate([
            'stars'=>'required',
            'hotel_id'=>'required',
        ]);

        $lastRate = HotelReview::where([
            'user_id'=>$request->user_id,
            'hotel_id'=>$request->hotel_id,
        ])->first();

        if($lastRate){
            return response()->json([
                'success'=>false,
                'message'=>'you can not rate this hotel more than one time',
            ]);
        }

        $comment = null;
        if(isset($request->comment)){
            $comment = $request->comment;
        }

        HotelReview::create([
            'stars'=>$request->stars,
            'comment'=>$comment,
            'user_id'=>$request->user_id,
            'hotel_id'=>$request->hotel_id,
        ]);

        //recalculating the rate of the hotel

        $hotel = Hotel::where('id',$request->hotel_id)->first();
        if(!$hotel){
            return response()->json([
                'success'=>false,
                'message'=>'hotel not found',
            ]);
        }

        $num_of_ratings = $hotel['num_of_ratings'];
        $rate = $hotel->rate;

        $new_rate = (($num_of_ratings*$rate)+$request->stars)/($num_of_ratings+1);

        Hotel::where('id',$request->hotel_id)
            ->update([
            'rate'=> $new_rate,
            'num_of_ratings'=> $num_of_ratings+1,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'review has sent successfully',
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        //
    }
}
