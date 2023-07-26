<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // This controller contains all operations the main admin can do on the Users.

    /**
     * Show All Users
     * @return JsonResponse
     */
    public function getAllUsers(): JsonResponse
    {
        $users = User::paginate(10);
        return response()->json([
            'success'=>true,
            'data'=>$users,
        ]);
    }


    /**
     * User Information
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:users',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $user = User::where('id','=',$request->id)->first();

        return $this->success($user,'Done successfully');
    }


    /**
     * Add User
     * @param Request $request
     * @return JsonResponse
     */
    public function addUser(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|unique:users|email',
            'password'=>'required|confirmed',
            'phone_number'=>'required',
            'wallet'=>'required',
            'points'=>'required',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $user = User::create($request->all());

        return $this->success($user,'User created successfully');
    }


    /**
     * Delete Some User
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteUserAccount(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:users',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $user = User::where('id','=',$request->id)->first();

        if(isset($user)){
            User::where('id','=',$request->id)->delete();
            return $this->success(null,'User deleted successfully');
        }

        return $this->error('User not found');
    }


    /**
     * Edit Some User
     * @param Request $request
     * @return JsonResponse
     */
    public function editUserAccount(Request $request): JsonResponse
    {
        $validated_data = Validator::make($request->all(), [
            'id' => 'required|exists:users',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $user = User::findOrFail($request->id);
        $user->fill($request->all());
        $user->save();

        return $this->success(null,'User updated successfully');
    }

}
