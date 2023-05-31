<?php

//use App\Http\Controllers\HotelController;
use App\Http\Controllers\Hotel\AdminController;
use App\Http\Controllers\Hotel\UserController;
use App\Http\Controllers\Hotel\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Hotel Admin Routes:
Route::post('hotel/register',[AdminController::class, 'CreateHotel']);
Route::post('hotel/login',[AdminController::class, 'HoltelLogin']);
Route::post('createhotel/{id}',[AdminController::class, 'CreateHotel']);

//Hotel user Routes:
Route::post('user/register',[UserController::class, 'Register']);
Route::post('user/login',[UserController::class, 'Login']);
Route::get('hotel/ShowAllHotel',[HotelController::class, 'ShowALLHotel']);


Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

    Route::post('dashboard',[HotelController::class, 'dashboard'])->name('hotel.dashboard');
});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){


    
});
