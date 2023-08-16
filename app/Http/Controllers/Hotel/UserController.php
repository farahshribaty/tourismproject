<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Users\UserController as UsersUserController;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\City;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Facilities;
use App\Models\HotelReview;
use Illuminate\Http\Request;
use App\Models\HotelReservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;


class UserController extends UsersUserController
{
    public function Register(Request $request) :JsonResponse
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

    public function Login(Request $request) :JsonResponse
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

        if ($request->has('word')) {
            $word = $request->input('word');
            $query->where(function ($query) use ($word) {
                $query->where('name', 'like', '%' . $word . '%')
                    ->orWhere('location', 'like', '%' . $word . '%')
                    ->orWhereHas('city', function ($query) use ($word) {
                        $query->where('name', 'like','%'.$word.'%');
                    })
                    ->orWhereHas('city.country', function ($query) use ($word) {
                        $query->where('name', 'like', '%'.$word.'%');
                    });
            });
        }
        if ($request->has('num_of_rooms')) {
            $query->where('num_of_rooms', '>=', $request->input('num_of_rooms'));
        }

        if ($request->has('check_in') && $request->has('check_out')) {
            $checkIn = $request->input('check_in');
            $checkOut = $request->input('check_out');

            $query->whereHas('Room', function ($que) use ($checkIn, $checkOut) {
                $que->whereDoesntHave('Reservations', function ($q) use ($checkIn, $checkOut) {
                    $q->where('hotel_reservations.check_in', '<=', $checkOut)
                        ->Where('hotel_reservations.check_out', '>=', $checkIn);
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
                $que->select('id','name');
            }]);
            } , 'photo', 'facilities'])
        ->where('id',$request->id)
        ->get();

        $reviews = HotelReview::where('hotel_id',$request->id)
        ->with('user',function($q){
            $q->select(['id','first_name','last_name','photo']);
        })
            ->paginate(6);

        $roomsByType = DB::table('rooms')
        ->join('room_types', 'rooms.room_type', '=', 'room_types.id')
        ->select('name as room_type','rooms.beds','price_for_night', DB::raw('COUNT(*) as room_count'))
        ->groupBy('room_types.name','rooms.beds','price_for_night')
        ->where('hotel_id', $request->id)
        ->get();

        $location = $hotel[0]['location'];
        $nearestHotels = Hotel::where('Hotels.location','=',$location)
        ->with(['photo','city'=> function ($query) {
            $query->select('id','name','country_id')
            ->with(['country' => function ($q) {
                $q->select('id','name');
            }]);
        }])
        ->paginate(4);
        $nearestHotels = $nearestHotels->makeHidden(['email','phone_number',
       'details','website_url','created_at','updated_at']);

        return response([
            'Hotel_info'=>$hotel,
            'Rooms'=>$roomsByType,
            'Reviews'=>$reviews,
            'Nearest_Hotels'=>$nearestHotels
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
    /**
     * Booking a Room in a Hotel with all the checking functions:
     * @param HotelReservation $request
     * @return JsonResponse
     */
    public function bookingRoom(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name'=>'required',
            'last_name'=>'required',
            'check_or_book' =>'required|in:book,check',
            'room_id' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
            'num_of_adults' => 'required|integer',
            'num_of_children' => 'required|integer',
            'with_discount'=> 'required_if:check_or_book,==,book|in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $info = $validator->validated();


        $room = Room::select('rooms.*')
        ->where('id', '=', $request->room_id)->first();

        if (!$room) {
            return $this->error('Room not found', 400);
        }

        // Check if the room is available for the specified dates
        if (!$this->checkRoomAvailability($info)) {
            return $this->error('The room is not available for the selected dates.
            Please choose different dates.');
        }

        $user_id= $request->user()->id;
        $hasMoney = $this->checkMoneyAvailability($info, $user_id);
        if ($hasMoney == -1) {
            return $this->error('You do not have enough money.');
        }
        //  Check if the room can accommodate the specified number of adults and children
        if (!$this->checkRoomCapacity($info)) {
            return $this->error('The room does not have enough capacity for the specified number of adults and children.');
        }
        // end of checks.

        $one_dollar_equals = 0.01;

        $booking_info = [
            'user_id' => auth()->id(),
            'first_name'=> $info['first_name'],
            'last_name'=> $info['last_name'],
            'room_id' => $info['room_id'],
            'hotel_id' => $room['hotel_id'],
            'check_in' => $info['check_in'],
            'check_out' => $info['check_out'],
            'num_of_adults'=> $info['num_of_adults'],
            'num_of_children'=> $info['num_of_children'],
            'price'=> $hasMoney,
            'payment' => $hasMoney,
            'points_added' => (int)($hasMoney * $one_dollar_equals),
        ];

        $one_point_equals = 10; // one point equals 10 dollars
        $discount = min($booking_info['payment'],$request->user()->points * $one_point_equals);
        $booking_info['payment_with_discount'] = $booking_info['payment']-$discount;

        if($request->check_or_book == 'check'){
            //return $this->success($booking_info,'When you press on book button, the room will be reserved with the following Info:');
            if($request->user()->points == 0){
                unset($booking_info['payment_with_discount']);
                return $this->success($booking_info,'When you press on book button, a ticket will be reserved with the following Info:');
            }
            else{
                return response()->json([
                    'message'=> 'When you press on book button, a ticket will be reserved with the following Info:',
                    'data'=> $booking_info,
                    'message1'=> 'Would you like to get benefit of your points?',
                ]);
            }
        }
        else{
            if($request->with_discount == 'yes' || $request->user()->wallet<$booking_info['payment']){
                $booking_info['payment'] = $booking_info['payment_with_discount'];
            }
            else{
                $discount = 0;
            }

            unset($booking_info['payment_with_discount']);
            HotelReservation::create($booking_info);

            $user = Auth::user();
            User::where('id',$request->user()->id)
                ->update([
                    'wallet'=> $request->user()->wallet - $booking_info['payment'],
                    'points'=> $request->user()->points - ($discount/$one_point_equals) + $booking_info['points_added'],
                ]);

            return $this->success($booking_info, 'Room reserved successfully with the following info:', 200);
        }
    }

    private function checkMoneyAvailability($info,$user_id)
    {
        $room = Room::where('id','=',$info['room_id'])->first();
        $checkInDate = Carbon::parse($info['check_in']);
        $checkOutDate = Carbon::parse($info['check_out']);

        $numberOfNights = $checkOutDate->diffInDays($checkInDate);

        $numberOfNights++;
        $moneyNeeded = $numberOfNights * $room['Price_for_night'];

        if($this->checkWallet($moneyNeeded,$user_id)){
            return $moneyNeeded;
        }
        else return -1;
    }

    private function checkRoomAvailability($info) :bool
    {
        $checkInDate = $info['check_in'];
        $checkOutDate = $info['check_out'];
        $room = Room::where('id','=',$info['room_id'])->first();

        $existingReservations = HotelReservation::select('hotel_reservations.*')
        ->where(function ($query) use ($room,$checkInDate, $checkOutDate) {
            $query->where('room_id','=', $room['id'])
                ->where('check_in', '<=', $checkOutDate)
                ->Where('check_out', '>=', $checkInDate);
            }
        )->exists();

        return !$existingReservations;
    }

    private function checkRoomCapacity($info): bool
    {
        $room = Room::where('id', '=', $info['room_id'])->first();

        if (!$room) {
            return false; // Room not found
        }

        $adults = $info['num_of_adults'];
        $children = $info['num_of_children'];

        $totalCapacity = $room['Sleeps'] + $room['Beds'];

        return ($adults + $children) <= $totalCapacity;
    }
}
