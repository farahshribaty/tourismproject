<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Users\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
//Route::get('hello',function(){
//    return 'hi';
//});
//
//Route::post('user/login',[UserLoginController::class, 'userLogin'])->name('user.login');
//
//// AUTHENTICATION API FOR USE
//Route::group( ['prefix' => 'user','middleware' => ['auth:user-api','scopes:user'] ],function(){
//
//    Route::post('dashboard',[UserLoginController::class, 'userDashboard'])->name('user.dashboard');
//});

Route::post('register',[UserController::class,'register1']);
Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth:user-api');

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
//
//// Resend link to verify email
//Route::post('/email/verify/resend', function (Request $request) {
//    $request->user()->sendEmailVerificationNotification();
//    return back()->with('message', 'Verification link sent!');
//})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

//Auth::routes([
//    'verify'=>true,
//]);

//hello world
