<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\UserLoginRequest;
use App\Http\Requests\UserRequest\UserRegistrationRequest;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * User Registration
     *
     * @param UserRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(UserRegistrationRequest $request)
    {
        $info = $request->validated();
        User::create($info);

        $user = User::where('email','=',$info['email'])->first();
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
    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        if(Auth::guard('user')->attempt(['email' => $data['email'], 'password' => $data['password']])){

            $user = User::where('email','=',$request->email)->first();
            $user['token'] = $user->createToken('MyApp')->accessToken;
            return response()->json([
                'success'=>true,
                'data'=>$user,
            ],200);
        }
        else{
            return response()->json([
                'success'=>false,
                'message'=>'incorrect credentials',
            ],400);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success'=>true,
            'message'=>'logged out successfully',
        ]);
    }
}
