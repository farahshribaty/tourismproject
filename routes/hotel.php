<?php

use App\Http\Controllers\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorLoginController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('hotel/register',[HotelController::class, 'register'])->name('doctor.login');

Route::group( ['prefix' => 'hotel','middleware' => ['auth:doctor-api'] ],function(){

    Route::post('dashboard',[HotelController::class, 'dashboard'])->name('doctor.dashboard');
});

