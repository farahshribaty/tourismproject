<?php

//use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminController;
//use App\Http\Controllers\Hotel\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('createadmin',[AdminController::class, 'CreateAdmin']);
Route::post('adminLogin',[AdminController::class, 'AdminLogin']);
Route::post('country',[AdminController::class, 'AddCountry']);
Route::post('city/{id}',[AdminController::class, 'AddCity']);
Route::get('showcity/{id}',[AdminController::class, 'ShowCities']);



Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api'] ],function(){

    //Route::post('createhotel',[AdminController::class, 'CreateHotel']);
    
    
});