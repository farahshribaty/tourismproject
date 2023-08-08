<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest\UserLoginRequest;
use App\Http\Requests\UserRequest\UserRegistrationRequest;
use App\Models\Attraction;
use App\Models\FlightsReservation;
use App\Models\Hotel;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use http\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Nette\Utils\DateTime;
use PhpParser\Error;

class UserController extends Controller
{
    /**
     * User Registration
     *
     * @param UserRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|unique:users|email',
            'password'=>'required|confirmed',
            'phone_number'=>'required|min:10|max:15',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        if ($request->hasFile('image')) {
            $file_extension = $request->image->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extension;
            $path = 'images/user';
            $request->image->move($path, $file_name);
            $request['photo'] = 'http://127.0.0.1:8000/images/user/' . $file_name;
        }

        $request['points'] = 0;
        $request['wallet'] = 100000;

        $user = User::create($request->all());

        $user['token'] = $user->createToken('MyApp')->accessToken;

        return response()->json([
            'success'=>'true',
            'data'=>$user,
        ],200);
    }

    public function register1(UserRegistrationRequest $request): JsonResponse
    {
        $info = $request->validated();
        $info['points']=0;
        $info['wallet']=100000;
        User::create($info);

        $user = User::where('email','=',$info['email'])->first();
        // send verification email
        event(new Registered($user));

        $user['token'] = $user->createToken('MyApp')->accessToken;

        return response()->json([
            'success'=>'true',
            'data'=>$user,
        ],200);
    }

    /**
     *  User Login
     *
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email','=',$request->email)->first();

        if($user){
       //            return $user['password'];
            if(Hash::check($request->password,$user['password'])){
                $user['token'] = $user->createToken('MyApp')->accessToken;
                return response()->json([
                    'success'=>true,
                    'data'=>$user,
                ]);
            }
            else{
                return response()->json([
                    'success'=>false,
                    'message'=>'incorrect password',
                ]);
            }
        }
        else{
            return response()->json([
                'success'=>false,
                'message'=>'email is not valid',
            ]);
        }
    }

    /**
     * User Logout
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success'=>true,
            'message'=>'logged out successfully',
        ]);
    }

    /**
     * Show Profile
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->makeHidden('wallet');
        return $this->success($user);
    }

    /**
     * Update Profile Info
     * @param Request $request
     * @return JsonResponse
     */
    public function editProfileInfo(Request $request): JsonResponse
    {
        if(isset($request['email'])) unset($request['email']);
        if(isset($request['password'])) unset($request['password']);
        if(isset($request['wallet'])) unset($request['wallet']);
        if(isset($request['points'])) unset($request['points']);

        User::where('id',$request->user()->id)->update($request->toArray());

        return $this->success(null,'Profile updated successfully');
    }

    /**
     * Update Profile Photo
     * @param Request $request
     * @return JsonResponse
     */
    public function editProfilePhoto(Request $request): JsonResponse
    {
        if ($request->hasFile('image')) {
            $file_extension = $request->image->getClientOriginalExtension();
            $file_name = time() . '.' . $file_extension;
            $path = 'images/user';
            $request->image->move($path, $file_name);
            $request['photo'] = 'http://127.0.0.1:8000/images/user/' . $file_name;
        }

        User::where('id',$request->user()->id)->update([
            'photo'=> $request['photo'],
        ]);

        return $this->success(null,'Profile photo updated successfully');
    }

    /**
     * Main Page For The Website
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $top_trips = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
            'destination'=>function($q){
                $q->with(['country']);
            }
            ])
            ->availableTrips()
            ->orderBy('rate','desc')
            ->take(6)
            ->get();

        $trip_offers = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                },
                'offer',
            ])
            ->whereHas('offers',function($query){
                $time = Carbon::now();
                $query->where('offer_end','>=',$time)
                    ->where('active',true);
            })
            ->availableTrips()
            ->take(6)
            ->get();

        $top_attractions = Attraction::select(['id','city_id','name','rate','num_of_ratings','adult_price','child_price'])
            ->with(['photo','city'])
            ->orderBy('rate','desc')
            ->take(6)
            ->get();

        $top_hotels = Hotel::orderBy('rate','desc')
            ->with(['photo','city'=> function ($query) {
             $query->select('id','name','country_id')
             ->with(['country' => function ($q) {
             $q->select('id','name');
                }]);
            }])
            ->take(6)
            ->get();

           $top_hotels = $top_hotels->makeHidden(['email','phone_number',
           'details','website_url','created_at','updated_at']);

        $popularCountries = FlightsReservation::select('countries.id', 'countries.name','countries.path', DB::raw('count(*) as total'))
        ->join('flights_times', 'flights_reservations.flights_times_id', '=', 'flights_times.id')
        ->join('flights', 'flights_times.flights_id', '=', 'flights.id')
        ->join('countries', 'flights.distination', '=', 'countries.id')
        ->groupBy('countries.id', 'countries.name','countries.path')
        ->orderByDesc('total')
        ->take(5) // Get top 5 popular countries
        ->get();

        return response()->json([
            'success'=>true,
            'top_attractions'=>$top_attractions,
            'top_trips'=>$top_trips,
            'trip_offers'=>$trip_offers,
            'top_hotels'=> $top_hotels,
            'popularCountries'=> $popularCountries
        ]);
    }

    /**
     * Search Function For Hotels, Attractions, Trips.
     * @param Request $request
     * @return JsonResponse
     */
    public function searchForAll(Request $request): JsonResponse
    {
        $word = $request->word;

        $hotels = Hotel::where('name','like','%'.$word.'%')
            ->orWhereHas('City',function($q)use($word){
                $q->where('name','like','%'.$word.'%')
                    ->orWhereHas('country',function($que)use($word){
                        $que->where('name','like','%'.$word.'%');
                    });
            })
            ->with(['photo', 'city', 'city.country', 'type','facilities'])
            ->take(6)->get();


        $attractions = Attraction::where('name','like','%'.$word.'%')
            ->orWhereHas('city',function($q)use($word){
                $q->where('name','like','%'.$word.'%')
                    ->orWhereHas('country',function($que)use($word){
                        $que->where('name','like','%'.$word.'%');
                    });
            })
            ->with(['photo', 'city'])
            ->take(6)->get();

        foreach($attractions as $attraction){
            $date1 = $attraction['open_at'];
            $date2 = $attraction['close_at'];

            $new_date1 = DateTime::createfromformat('Y-m-d H:i:s',$date1);
            $new_date2 = DateTime::createfromformat('Y-m-d H:i:s',$date2);

            $attraction['open_at'] = $new_date1->format('H:i');
            $attraction['close_at'] = $new_date2->format('H:i');
        }



        $trips = Trip::select(['id','destination','description','details','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->where('description','like','%'.$word.'%')
            ->orWhereHas('destination',function($q)use($word){
                $q->where('name','like','%'.$word.'%')
                    ->orWhereHas('country',function($que)use($word){
                        $que->where('name','like','%'.$word.'%');
                    });
            })
            ->with(['photo',
                'destination',
                'destination.country'
            ])
            ->availableTrips()
            ->take(6)->get();

        return response()->json([
            'success'=>true,
            'hotels'=>$hotels,
            'attractions'=>$attractions,
            'trips'=>$trips,
        ]);
    }

    // todo: add hotels and flights to this function
    /**
     * Add To Favourites
     * @param Request $request
     * @return JsonResponse
     */
    public function addToFavourites(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'section_id'=> 'required|between:0,3',
            'section_type'=> 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $sections = ['trip_favourites','attraction_favourites','hotels','flights'];     // Those are the names of 4 tables each one for its section favourites.
        $ids = ['trip_id','attraction_id','hotel_id','flight_id'];     // Those are the names of section_id column in the last 4 tables.

        // check if they're already in favourites:

        $last_one = DB::table($sections[$request->section_type])->where('user_id',$request->user()->id)->where($ids[$request->section_type],$request->section_id)->first();

        if(isset($last_one)){
            return $this->error('It is already in your favourites!');
        }

        // putting it in favourites:

        DB::table($sections[$request->section_type])->insert([
            'user_id'=> $request->user()->id,
            $ids[$request->section_type] => $request->section_id,
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Added to favourites successfully'
        ]);
    }

    // todo: add hotels and flights to this function
    /**
     * Remove From Favourites
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFromFavourites(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'section_id'=> 'required|between:0,3',
            'section_type'=> 'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $sections = ['trip_favourites','attraction_favourites','hotels','flights'];     // Those are the names of 4 tables each one for its section favourites.
        $ids = ['trip_id','attraction_id','hotel_id','flight_id'];     // Those are the names of section_id column in the last 4 tables.

        DB::table($sections[$request->section_type])->where('user_id',$request->user()->id)->where($ids[$request->section_type],$request->section_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trip removed from favourites successfully',
        ]);
    }

    // todo: add hotels and flights to this function
    /**
     * Show Favourite List Of The 4 Sections
     * @param Request $request
     * @return JsonResponse
     */
    public function getFavouriteList(Request $request): JsonResponse
    {
        $attractions = Attraction::select(['id','city_id','name','rate','num_of_ratings','adult_price','child_price'])
            ->with(['photo','city'])
            ->whereHas('followers',function($q)use($request){
            $q->where('user_id',$request->user()->id);
        })->take(4)->get();

        $trips = Trip::select(['id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
            ->whereHas('followers',function($q)use($request){
            $q->where('user_id',$request->user()->id);
        })->take(4)->get();

        return response()->json([
            'success'=>'true',
            'attractions'=> $attractions,
            'trips'=> $trips,
        ]);
    }

    // todo: add hotels and flights to this function
    /**
     * Show Last Reservations Of The 4 Sections
     * @param Request $request
     * @return JsonResponse
     */
    public function getLastReservations(Request $request): JsonResponse
    {
        $attractions = Attraction::select(['attractions.id','attraction_reservations.id as reservation_id','city_id','name','rate','num_of_ratings','adult_price','child_price','book_date','payment'])
            ->with(['photo','city'])
            ->join('attraction_reservations','attraction_reservations.attraction_id','=','attractions.id')
            ->where('attraction_reservations.user_id','=',$request->user()->id)
            ->take(4)
            ->get();

        $trips = Trip::select(['trips.id','trips_reservations.id as reservation_id','destination','description','days_number','rate','num_of_ratings','max_persons','start_age','end_age','departure_date','money_spent'])
            ->with(['photo',
                'destination'=>function($q){
                    $q->with(['country']);
                }
            ])
            ->join('trip_dates','trip_dates.trip_id','=','trips.id')
            ->join('trips_reservations','trips_reservations.date_id','=','trip_dates.id')
            ->where('trips_reservations.user_id','=',$request->user()->id)
            ->take(4)
            ->get();


        return response()->json([
            'success'=> true,
            'attractions'=> $attractions,
            'trips'=> $trips,
        ]);
    }




    // helpful functions:

    public function checkWallet($money_needed,$user_id): bool
    {
        $user = User::where('id','=',$user_id)->first();

        if(!$user){
            return false;
        }
        $wallet = $user['wallet'];
        if($money_needed>$wallet){
            return false;
        }
        else{
            return true;
        }
    }
}
