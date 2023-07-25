<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AirlineAdmin;
use App\Models\Attraction;
use App\Models\AttractionAdmin;
use App\Models\Country;
use App\Models\City;
use App\Models\Hotel;
use App\Models\HotelAdmin;
use App\Models\TripAdmin;
use App\Models\TripCompany;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Dotenv\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $tables = ['main_admin','airline_admin','hotel_admin','attraction_admin','trip_admin'];
        $admins[0] = Admin::where('user_name','=',$request->user_name)->first();
        $admins[1] = AirlineAdmin::where('user_name','=',$request->user_name)->first();
        $admins[2] = HotelAdmin::where('user_name','=',$request->user_name)->first();
        $admins[3] = AttractionAdmin::where('user_name','=',$request->user_name)->first();
        $admins[4] = TripAdmin::where('user_name','=',$request->user_name)->first();

        $i=0;
        foreach($admins as $admin){
            if(isset($admin)){
//                if(Hash::check($request->password,$admin->password)){
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
