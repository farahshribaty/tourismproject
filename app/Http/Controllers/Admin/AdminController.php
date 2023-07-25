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
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;

class AdminController extends Controller
{
    public function CreateAdmin(Request $request) //done
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

    function login(Request $request)
    {
        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);

        $sections = ['hotel_admin', 'airline_admin', 'trip_admin', 'attraction_admin'];

        // Attempt authentication against each section table
        foreach ($sections as $section) {
            $response = Http::post('http://127.0.0.1:8000' . '/oauth/token', [
                'grant_type' => 'password',
//                'client_id' => config('passport.password_client_id'),
//                'client_secret' => config('passport.password_client_secret'),
                'client_id' => 4,
                'client_secret' => 'LnSYxgNXqH2aXHIQZdRxRviC9UhqbCVUykCqBaIv',
                'username' => $request->email,
                'password' => $request->password,
                'scope' => $section,
            ]);
            return $response;

            if ($response->successful()) {
                // If authentication succeeds, return the authenticated user
                return Auth::guard($section)->user();
            }
        }

        // If authentication fails for all sections, return null
        return 'failed';
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
