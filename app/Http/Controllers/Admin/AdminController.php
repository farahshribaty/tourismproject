<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Country;
use App\Models\City;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;

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
        $admin->password = $request->password;
        $admin->phone_number = $request->phone_number;


        $admin->save();

        $accessToken=$admin->createtoken('MyApp',['admin'])->accessToken;
    
          return response()->json([
                   'admin'=> $admin,
                   'access_token'=>$accessToken
            ]);
    }

    public function AdminLogin(Request $request) //mo zapet
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if(auth()->guard('admin')->attempt($request->only('email','password')))
        {
            config(['auth.guards.api.provider'=>'admin']);
            $admin=Admin::query()->select('admins.*')->find(auth()->guard('admin')->user()['id']);
            $success=$admin;
            $success['token']=$admin->createtoken('MyApp',['admin'])->accessToken;
            return response()->json($success);
        }
        else
         {
            return response()->json(['error' => ['Email and Password are Wrong.']], 200);
         }
    }
  
    public function CreateHotel(Request $request) //done
    {
        $request->validate([
            'name'=>['required','max:55'],
            'email'=>['email','required','unique:hotels'],
            'password'=>[
                'required',
               'confirmed',
               password::min(8)
                ->letters()
                ->numbers()
                ->symbols()
             ]
            ]);     
        
            
            $hotel = new Hotel();
            $hotel->name = $request->name;
            $hotel->email = $request->email;
            $hotel->password = bcrypt($request->password);
            
            $hotel->save();

          $accessToken=$hotel->createtoken('MyApp',['hotel'])->accessToken;
    
          return response()->json([
                   'hotel'=> $hotel,
                   'access_token'=>$accessToken
            ]);
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

    public function AddCity(Request $request)
    {
        $country_id=auth()->id();
        $request->validate([
            'name'=>'required',
            'country_id'
        ]);

        $city =new City();
        $city->name = $request->name;
        $city->country_id =$country_id;
        $city->save();
        
        return response()->json([
            'city'=> $city,
            'message'=>"city added successfuly"
     ]);

    }

    public function store(Request $request)
    {
        //
    }

    public function show(Admin $admin)
    {
        //
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
