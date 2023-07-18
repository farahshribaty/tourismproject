<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
//            return redirect(env('FRONT_URL') . '/email/verify/already-success');
            return response()->json([
                'message'=>'email has already been verified.',
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
//
//        return redirect(env('FRONT_URL') . '/email/verify/success');
        return response()->json([
            'message'=>'email verified successfully.',
        ]);
    }
}
