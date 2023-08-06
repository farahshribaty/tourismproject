<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Facilities;
use App\Models\Hotel;
use App\Models\City;
use App\Models\HotelReview;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function Register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'max:55'],
            'last_name' => ['required', 'max:55'],
            'email' => ['email', 'required'/*,'unique:users'*/],
            'password' => [
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

        $accessToken = $user->createtoken('MyApp', ['user'])->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $accessToken
        ]);
    }

    public function Login(Request $request)
    {
        $request->validate([

            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->guard('user')->attempt($request->only('email', 'password'))) {
            config(['auth.guards.api.provider' => 'user']);
            $user = User::query()->select('users.*')->find(auth()->guard('user')->user()['id']);
            $success = $user;
            $success['token'] = $user->createtoken('MyApp', ['user'])->accessToken;
            return response()->json($success);
        } else {
            return response()->json(['error' => ['unauthorized']], 401);
        }
    }

    public function ShowCities(Request $request) //done
    {
        $cities = City::where('cities.country_id', '=', $request->id)
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
    
    /**
     * Search for Hotels by user
     */
    public function Hotelsearch(Request $request)
    {
        $query = Hotel::query();
        $num_of_adults = $request->input('num_of_adults');
        $num_of_children = $request->input('num_of_children');

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

            $query->whereHas('Room', function ($que) use ($checkIn, $checkOut) {
                $que->whereDoesntHave('Reservations', function ($q) use ($checkIn, $checkOut) {
                    $q->where('hotel_resevations.check_in', '<=', $checkOut)
                        ->Where('hotel_resevations.check_out', '>=', $checkIn);
                });
            });
        }
    
        if ($request->has('num_of_adults') && $request->has('num_of_children')) {
            $num_of_adults = $request->input('num_of_adults');
            $num_of_children = $request->input('num_of_children');

            $query->whereHas('Room',function ($query) use ($num_of_adults,$num_of_children) {
                $query->where(function ($query) use ($num_of_adults,$num_of_children) {
                    $query->where('Sleeps', '>=', $num_of_adults)
                          ->where('Beds', '>=', ($num_of_adults + $num_of_children));
                });
            });
        }

        // Rate Filters

        if ($request->has('rate')) {                    
            $query->where('rate', '=', $request->input('rate'));
        }

        // stars Filters

        if ($request->has('stars')) {                   
            $query->where('stars', '=', $request->input('stars'));
        }

        //price Filters

        if($request->has('max_price')){
            $query->whereHas('Room',function($que)use($request){
                $que->where('price_for_night','<=',$request->max_price);
            });
        }

        if($request->has('min_price')){
            $query->whereHas('Room',function($que)use($request){
                $que->where('price_for_night','>=',$request->min_price);
            });
        }

        // Facilities' Filters

        $facilities = Facilities::get();

        foreach($facilities as $facility){
            if($request->has($facility['name'])){
                $query->whereHas('Facilities',function($q)use($facility){
                    $q->where('name','=',$facility['name']);
                });
            }
        }

        // Types' Filters

        if($request->has('hotel_type')){
            $query->whereHas('Type',function($que)use($request){
                $que->where('name','=',$request->hotel_type);
            });
        }

        $hotels = $query
            ->with(['photo', 'city', 'city.country', 'type','facilities'])
            ->paginate(10);

        return response()->json([
            'message' => "done",
            'All_hotels' => $hotels,
        ]);
    }

    public function Reservations(Request $request)
    {
        $data = Hotel::where('id','=',$request->id)
            ->with('Room',function($query){
                $query->with('Reservations');
            })->get();

        return $data;
    }

    /**
     *Adding reviews By user
     */
    public function addReview(Request $request) //done
    {
        $request->validate([
            'rate'=>'required',
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
        if(isset($request->rate)){
            $rate = $request->rate;
        }

        HotelReview::create([
            'rate'=>$rate,
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
       
        $old_rate = $num_of_ratings*$hotel['rate'];
        $new_rate = ($old_rate+$request->rate)/($num_of_ratings+1);

        Hotel::where('id',$request->hotel_id)
            ->update([
            'rate'=> $new_rate,
            'num_of_ratings'=> $num_of_ratings+1,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'review has sent successfully',
            // 'old rate'=>$old_rate,
            // 'new rate'=>$new_rate
        ]);
    }
    /**
     * getting all the hotel info
     */
    public function getAllHotelInfo(Request $request)
    {
        $hotel = Hotel::with(['type', 'city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($que) {
                $que->select('id','name'); }]);
                } , 'photo', 'facilities','reviews'=> function($qu){
                $qu->select('id','hotel_id','rate','comment','user_id')
                ->with(['user' => function ($q) {
                $q->select('id','first_name','last_name'); 
                }]);
            }])
        ->where('id',$request->id)
        ->get();

        $roomsByType = DB::table('rooms')
        ->join('room_types', 'rooms.room_type', '=', 'room_types.id')
        ->select('name as room_type','rooms.beds', DB::raw('COUNT(*) as room_count'))
        ->groupBy('room_types.name','rooms.beds')
        ->where('hotel_id', $request->id)
        ->get();

        return response([
            'Hotel_info'=>$hotel,
            'Rooms'=>$roomsByType
        ]);
    }

    /**
     * show all rooms for this type
     */
    public function ShowOneRoom(Request $request)
    {
        $room =Room::join('room_types', 'rooms.room_type', '=', 'room_types.id')
        ->select('rooms.*', 'room_types.name as room_type')
        ->with(['features','photo','Hotel' /*=> function($qu) {
        $qu->select('id','name','email','location');
        }*/])
        ->where('hotel_id', $request->id)
        ->where('room_types.name', $request->selectedRoomType)
        ->first();


        return response([
            'Room_info'=>$room,
        ]);
    
    }

}
