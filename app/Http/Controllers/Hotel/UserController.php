<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    public function Register(Request $request)
    {
        $request->validate([
            'first_name'=>['required','max:55'],
            'last_name'=>['required','max:55'],
        'email'=>['email','required'/*,'unique:users'*/],
            'password'=>[
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

          $accessToken=$user->createtoken('MyApp',['user'])->accessToken;
    
          return response()->json([
                   'user'=> $user,
                   'access_token'=>$accessToken
            ]);
    }

    public function Login(Request $request)
    {
        $request->validate([

            'email'=>'required|email',
            'password'=>'required',
        ]);

        if(auth()->guard('user')->attempt($request->only('email','password'))){
            config(['auth.guards.api.provider'=>'user']);
            $user=User::query()->select('users.*')->find(auth()->guard('user')->user()['id']);
            $success=$user;
            $success['token']=$user->createtoken('MyApp',['user'])->accessToken;
            return response()->json($success);
    
        }
        else{
            return response()->json(['error'=>['unauthorized']],401);
        }
    }
    
    public function ShowCities(Request $request,$id) //done..select a certain country
    {
        $cities=City::where('cities.country_id','=',$id)
        ->get();

         return response()->json([
        'message' => $cities,
        ]);
    }
    public function rating(Request $request,$id)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        //
    }
}
