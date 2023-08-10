<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AirlineAdmin;
use App\Models\AttractionAdmin;
use App\Models\AttractionUpdating;
use App\Models\Country;
use App\Models\City;
use App\Models\HotelAdmin;
use App\Models\TripAdmin;
use App\Models\TripUpdating;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;


class AdminController extends Controller
{
    public function CreateAdmin1(Request $request) //done
    {
        $request->validate([
            'first_name'=>['required','max:55'],
            'last_name'=>['required','max:55'],
            'user_name'=>['required','unique:hotels'],
            'email'=>['email','required'],
            'password'=>[
                'required',
               password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
            ],
             'phone_number'=>['required']
            ]);

        $admin = new Admin();
        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->user_name = $request->user_name;
        $admin->password =$request->password;  //should add bcrypt() but it didn;t eork on the login bcuz of it
        $admin->phone_number = $request->phone_number;

        $admin->save();

        $accessToken=$admin->createtoken('MyApp',['admin'])->accessToken;

          return response()->json([
                   'admin'=> $admin,
                   'access_token'=>$accessToken
            ]);
    }

    public function CreateAdmin(Request $request) //new 
    {
        $request->validate([
        'user_name',
        'password'=>[
            'required',
           password::min(8)
            ->letters()
            ->numbers()
            ->symbols()
        ]
        ]);
        $type=$request->admin_type;
        if($type=='Hotel')
        {
            $admin = new HotelAdmin();
            $admin->user_name=$request->user_name;
            $admin->password=$request->password;
            $admin->save();
            $accessToken=$admin->createtoken('MyApp',['admin'])->accessToken;
        }
        if($type=='Airline')
        {
            $admin = new AirlineAdmin();
            $admin->user_name=$request->user_name;
            $admin->password=$request->password;
            $admin->save();
            $accessToken=$admin->createtoken('MyApp',['admin'])->accessToken;
        }
        if($type=='Trips')
        {
            $admin = new TripAdmin();
            $admin->user_name=$request->user_name;
            $admin->password=$request->password;
            $admin->save();
            $accessToken=$admin->createtoken('MyApp',['admin'])->accessToken;
        }
        if($type=='Attraction')
        {
            $admin = new AttractionAdmin();
            $admin->user_name=$request->user_name;
            $admin->password=$request->password;
            $admin->save();
            $accessToken=$admin->createtoken('MyApp',['admin'])->accessToken;
        }
        return response()->json([
            'admin'=> $admin,
            'access_token'=>$accessToken
        ]);

    }

    public function AdminLogin(Request $request) //MAin Admin Login
    {
        $request->validate([

            'email'=>'required|email',
            'password'=>'required',
        ]);
        $admin=Admin::where('email',$request->email)->first();

        if(!$admin)
        {
            return 'user not found';
        }
        if($admin['password']==$request->password)
        {
            Auth::login($admin);
            $hotel['token']=$admin->createtoken('MyApp')->accessToken;
            return response([
                'message'=>'admin loged in',
                'admin'=>$admin
            ]);

        }
        else
        {
            return 'password not found';
        }

    }

    public function AddCountry(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'path'=>'required'
        ]);

        $country =new Country();
        $country->name = $request->name;
        $country->path = $request->path;
        $country->save();

        return response()->json([
            'country'=> $country,
            'message'=>"country added successfuly"
        ]);
    }

    public function AddCity(Request $request) //done
    {
        // Find a country by its ID
        // $country = Country::find($request->country_id);

        $country = Country::where('id', $request->country_id)->first();

        if (!$country) {
            // Handle the case where the country ID does not exist
            return response()->json(['error' => 'Country not found'], 404);
        }

        // Add a new city to the country
        $city = new City;
        $city->name = $request->name;
        $city->country_id = $country->id;
        $city->save();

        // Return a response indicating success
        return response()->json([
            'city'=> $city,
            'message' => 'City added to country'], 200);

    }
    //this should work for users..:
    // public function ShowCities(Request $request)
    // {
    //     // Find a country by its ID
    //     // $country = DB::table('countries')->get();
    //     // ->where('id','=',$request->id)->get();
    //     $country_id = Country::find($request->country_id);
    //     // $country = Country::where('id', $request->country_id)->first();
    //     // if (!$country) {
    //     //     // Handle the case where the country ID does not exist
    //     //     return response()->json(['error' => 'Country not found',$country], 404);
    //     // }

    //     // Retrieve all cities in the country
    //     $cities = City::select('id','cities.*')
    //     ->where('cities.country_id', $country_id)->get();

    //     // Return the list of cities as a JSON response
    //     return response()->json([
    //         'cities' => $cities,
    //         'country'=>$country_id,
    //         $request->country_id
    //     ], 200);
    // }

    public function ShowCities(Request $request)// mo zapeeeeet
    {
        $country_id=DB::table('countries')
        ->select('countries.*')
        ->where('id',$request->id)->get();

        return response()->json([
            $country_id
        ]);
    }
    function login(Request $request)     // this login is for all admins
    {
        $validated_data = Validator::make($request->all(), [
            'user_name' => 'required',
            'password' => 'required',
        ]);

        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $tables = ['main_admin','airline_admin','hotel_admin','attraction_admin','trip_admin'];
        $admins[0] = Admin::where('user_name','=',$request->user_name)->first();
        $admins[1] = AirlineAdmin::where('user_name','=',$request->user_name)->first();
        $admins[2] = HotelAdmin::where('user_name','=',$request->user_name)->first();
        $admins[3] = AttractionAdmin::where('user_name','=',$request->user_name)->first();
        $admins[4] = TripAdmin::where('user_name','=',$request->user_name)->first();

        $i=0;
        foreach($admins as $admin){
            if(isset($admin)){
        //if(Hash::check($request->password,$admin->password)){
                  if($request->password == $admin['password']){
                      $admin['admin_type'] = $tables[$i];
                      $admin['token'] = $admin->createToken('MyApp')->accessToken;
                      return response()->json([
                          'success'=>true,
                          'admin'=>$admin,
                      ],200);
                  }
            }
            $i++;
        }

        return response()->json([
            'success'=>false,
            'message'=>'Incorrect email or password',
        ],400);
    }


    /**
     * Show Updating List For All Sections, With 'is_all_seen' Boolean Which Determines If All Updates Are Seen Or Not.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdatingList(Request $request): JsonResponse
    {
        $attractions = AttractionUpdating::select(['id','attraction_admin_id', 'add_or_update', 'accepted', 'rejected', 'seen', 'created_at'])
            ->with('admin')
            ->where('accepted',0)
            ->where('rejected',0)
            ->get()->toArray();

        $trips = TripUpdating::select(['id','trip_admin_id', 'add_or_update', 'accepted', 'rejected', 'seen', 'created_at'])
            ->with('admin')
            ->where('accepted',0)
            ->where('rejected',0)
            ->get()->toArray();

        $is_all_seen = 1;    // Initially, we suppose that all updates are seen.

        $all = [];
        foreach($trips as $trip){
            $trip['type'] = 'trip_company';
            $is_all_seen &= $trip['seen'];
            array_push($all,$trip);
        }
        foreach($attractions as $attraction){
            $attraction['type'] = 'attraction_company';
            $is_all_seen &= $attraction['seen'];
            array_push($all,$attraction);
        }

        // Sorting updates by created time.
        usort($all, function($a,$b)
        {
            $a_time = strtotime($a['created_at']);
            $b_time = strtotime($b['created_at']);
            return $a_time < $b_time;
        });

        return response()->json([
            'success'=> true,
            'is_all_seen'=> $is_all_seen,
            'data'=> $all,
        ]);
    }


    public function edit(Admin $admin)
    {
        //
    }

    public function update(Request $request, Admin $admin)
    {
        //
    }

    public function destroy(Admin $admin)
    {
        //
    }
}
