<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{

    public function showForgetPasswordForm()
    {
        return view('auth.forgetPassword');
    }


    // this function gets email address and send a link to enable reset password
    public function submitForgetPasswordForm(Request $request)
    {
        $validated_data = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if($validated_data->fails()){
            return response()->json(['error' => $validated_data->errors()->all()]);
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('email.forgetPassword', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

//        return back()->with('message', 'We have e-mailed your password reset link!');
        return response()->json([
            'success'=>true,
            'message'=>'We have e-mailed your password reset link!',
        ]);
    }



    public function showResetPasswordForm($token) {
        return view('auth.forgetPasswordLink', ['token' => $token]);
    }

//    public function showResetPasswordForm($token) {
//        return view('auth.passwords.reset', ['token' => $token]);
//    }


    // this function get the email and new password and reset it.
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

        if(!$updatePassword){
            return $this->error('An error occurred');
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email'=> $request->email])->delete();

        return $this->success('Password resat successfully');

//        return redirect('/login')->with('message', 'Your password has been changed!');
    }
}
