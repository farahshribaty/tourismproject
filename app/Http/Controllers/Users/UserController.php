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
            'phone_number'=>'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $user = User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'password'=>$request->password,
            'phone_number'=>$request->phone_number,
            'points'=>0,
            'wallet'=>100000,
        ]);

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

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success'=>true,
            'message'=>'logged out successfully',
        ]);
    }

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
            ->orderBy('rate','desc')
            ->with(['photo','city'])
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
        ->take(6) // Get top 5 popular countries
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

    public function searchForAll(Request $request): JsonResponse
    {
        $word = $request->word;

        $hotels = Hotel::select(['id','name','location','rate','num_of_ratings','city_id'])
            ->where('name','like','%'.$word.'%')
            ->orWhereHas('City',function($q)use($word){
                $q->where('name','like','%'.$word.'%')
                    ->orWhereHas('country',function($que)use($word){
                        $que->where('name','like','%'.$word.'%');
                    });
            })
            ->with(['City',
                'City.country'=>function($q) {
                    $q->select(['id', 'name']);
                },
                'onePhoto'])
            ->take(6)->get();


        $attractions = Attraction::select(['id','name','location','rate','num_of_ratings','adult_price','city_id'])
            ->where('name','like','%'.$word.'%')
            ->orWhereHas('city',function($q)use($word){
                $q->where('name','like','%'.$word.'%')
                    ->orWhereHas('country',function($que)use($word){
                        $que->where('name','like','%'.$word.'%');
                    });
            })
            ->with(['city',
                'city.country'=>function($q) {
                    $q->select(['id', 'name']);
                },
                'photo'])
            ->take(6)->get();



        $trips = Trip::select(['id','destination','description','days_number','rate','num_of_ratings'])
            ->where('description','like','%'.$word.'%')
            ->orWhereHas('destination',function($q)use($word){
                $q->where('name','like','%'.$word.'%')
                    ->orWhereHas('country',function($que)use($word){
                        $que->where('name','like','%'.$word.'%');
                    });
            })
            ->with([
                'destination',
                'destination.country'=>function($q) {
                    $q->select(['id', 'name']);
                },
                'photo'
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
