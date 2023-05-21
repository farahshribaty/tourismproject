<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLoginController;
use Illuminate\Http\Request;
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

Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth:user-api');

// changed
