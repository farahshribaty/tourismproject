<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest\UserLoginRequest;
use App\Http\Requests\UserRequest\UserRegistrationRequest;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

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
        $info['points']=0;
        $info['wallet']=100000;
        User::create($info);

        $user = User::where('email','=',$info['email'])->first();
        $user['token'] = $user->createToken('MyApp')->accessToken;

        return response()->json([
            'success'=>'true',
            'data'=>$user,
        ],200);
    }

    public function register1(UserRegistrationRequest $request)
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
    public function login(UserLoginRequest $request)
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
//        if(Auth::guard('user')->attempt(['email' => $data['email'], 'password' => $data['password']])){
//
//            $user = User::where('email','=',$request->email)->first();
//            $user['token'] = $user->createToken('MyApp')->accessToken;
//            return response()->json([
//                'success'=>true,
//                'data'=>$user,
//            ],200);
//        }
//        else{
//            return response()->json([
//                'success'=>false,
//                'message'=>'incorrect credentials',
//            ],400);
//        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success'=>true,
            'message'=>'logged out successfully',
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
