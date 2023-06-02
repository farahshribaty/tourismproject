<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Country;
use App\Models\City;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;
use Dotenv\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        $admin->password =bcrypt($request->password); 
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
            'password'=>'required',
        ]);
        $auth = Auth::guard('admin');
        return response()->json([
        'status'=>$auth->setUser(['email'=>$request->email,'password'=>$request->password])
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

    public function AddCity(Request $request,$id) //done
    {
       //$id=Country::where($id)->get();
        $id=DB::table('countries')
        ->where('countries.id','=',$id)
        ->get();
        //   $request->validate([
        //   'name'=>'required',
        //   'id'
        //         ]);
        
        $city =new City();
        $city->name = $request->name;
        $city->country_id =$id[0]->id;
        $city->save();

        return response()->json([
            'city'=> $city,
            'message'=>"city added successfuly"
     ]);

    }
    
    //this should work for users..:
    public function ShowCities(Request $request,$id) //done..select a certain country
    {
        $cities=City::where('cities.country_id','=',$id)
        ->get();

         return response()->json([
        'message' => $cities,
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
