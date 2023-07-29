<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attraction;
use App\Models\Country;
use App\Models\City;
use App\Models\Hotel;
use App\Models\TripCompany;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Dotenv\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\Airline;
use App\Models\AirlineAdmin;
use App\Models\AttractionAdmin;
use App\Models\HotelAdmin;
use App\Models\TripAdmin;

class AdminController extends Controller
{
    public function CreateAdmin1(Request $request) //old
    {
        $request->validate([
            'first_name'=>['required','max:55'],
            'last_name'=>['required','max:55'],
            'email'=>['email','required','unique:hotels'],
            'password'=>[
                'required',
               'confirmed',
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
        $admin->email = $request->email;
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
        ]);

        $country =new Country();
        $country->name = $request->name;
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
    public function ShowCities(Request $request)
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

    public function getAllUsers()
    {
        $users = User::paginate(10);
        return response()->json([
            'success'=>true,
            'data'=>$users,
        ]);
    }

    public function getAllTripCompanies()
    {
        $companies = TripCompany::paginate(10);
        return response()->json([
            'success'=>true,
            'data'=>$companies,
        ]);
    }

    public function getAllAttractions()
    {
        $attractions = Attraction::paginate(10);
        return response()->json([
            'success'=>true,
            'data'=>$attractions,
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
