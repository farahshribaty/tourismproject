<?php

//use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminController;
//use App\Http\Controllers\Hotel\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('createadmin',[AdminController::class, 'CreateAdmin']);
Route::post('adminLogin',[AdminController::class, 'AdminLogin']);
Route::post('country',[AdminController::class, 'AddCountry']);
Route::post('city',[AdminController::class, 'AddCity']);
Route::get('showcity',[AdminController::class, 'ShowCities']);
Route::get('getAllUsers',[AdminController::class,'getAllUsers']);

Route::post('login',[AdminController::class,'login']);

Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api'] ],function(){

});
