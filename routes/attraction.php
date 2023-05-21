<?php

use App\Http\Controllers\AttractionController;
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

Route::post('attraction/register',[AttractionController::class, 'register'])->name('attraction.login');

Route::group( ['prefix' => 'attraction','middleware' => ['auth:attraction-api'] ],function(){

    Route::post('dashboard',[AttractionController::class, 'dashboard'])->name('attraction.dashboard');
});
