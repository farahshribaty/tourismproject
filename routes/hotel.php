<?php

use App\Http\Controllers\HotelController;
use App\Http\Controllers\Hotel\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorLoginController;


Route::post('hotel/register',[AdminController::class, 'CreateHotel']);
Route::post('hotel/login',[HotelController::class, 'HoltelLogin']);

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

    Route::post('dashboard',[HotelController::class, 'dashboard'])->name('hotel.dashboard');
});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){


    
});
