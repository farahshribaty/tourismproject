<?php

use App\Http\Controllers\HotelController;
use App\Http\Controllers\TripsController;
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

Route::post('trip/register',[TripsController::class, 'register'])->name('trip.login');

Route::group( ['prefix' => 'trip','middleware' => ['auth:trip_company-api'] ],function(){

    Route::post('dashboard',[TripsController::class, 'dashboard'])->name('trip.dashboard');
});
