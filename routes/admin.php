<?php

//use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('createadmin',[AdminController::class, 'CreateAdmin']);
Route::post('adminLogin',[AdminController::class, 'AdminLogin']);

Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api'] ],function(){

    Route::post('createhotel',[AdminController::class, 'CreateHotel']);
    
    Route::post('country',[AdminController::class, 'AddCountry']);
    Route::post('city',[AdminController::class, 'AddCity']);
    
});