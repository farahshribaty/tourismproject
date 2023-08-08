<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\Users\ForgotPasswordController;
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


Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::get('index',[UserController::class,'index']);
Route::post('searchForAll',[UserController::class,'searchForAll']);

Route::group(['middleware' => ['auth:user-api']], function () {
    Route::post('logout',[UserController::class,'logout']);
    Route::post('addToFavourites',[UserController::class,'addToFavourites']);
    Route::post('removeFromFavourites',[UserController::class,'removeFromFavourites']);
    Route::get('profile',[UserController::class,'profile']);
    Route::post('editProfileInfo',[UserController::class,'editProfileInfo']);
    Route::post('editProfilePhoto',[UserController::class,'editProfilePhoto']);
    Route::get('getFavouriteList',[UserController::class,'getFavouriteList']);
    Route::get('getLastReservations',[UserController::class,'getLastReservations']);
});


// Email verification routes
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Reset password routes
Route::group(['middleware' => ['web']], function () {
    Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
//    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
});
