<?php


//use App\Http\Controllers\HotelController;
use App\Http\Controllers\Hotel\AdminController;
use App\Http\Controllers\Hotel\UserController;
use App\Http\Controllers\Hotel\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Hotel Admin Routes:
Route::post('admin/login',[AdminController::class, 'AdminLogin']);
Route::post('admin/createhotel',[AdminController::class, 'CreateHotel']);
Route::post('admin/addRoom',[AdminController::class, 'addRooms']);
Route::post('admin/addPhoto',[AdminController::class, 'addPhotos']);
Route::post('admin/addRoomPhoto',[AdminController::class, 'addRoomPhotos']);

//Hotel user Routes:
Route::post('user/register',[UserController::class, 'Register']);
Route::post('user/login',[UserController::class, 'Login']);
Route::post('user/addReview',[UserController::class, 'addReview']);

Route::get('TopRated',[HotelController::class, 'TopRated']);
Route::get('ShowHotelTypes',[HotelController::class, 'ShowHotelTypes']);
Route::get('ShowRoomsTypes',[HotelController::class, 'ShowRoomsTypes']);
Route::get('ShowHotelRooms',[HotelController::class, 'ShowHotelRooms']);
Route::get('ShowAllHotel',[HotelController::class, 'ShowALLHotel']);





Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

    Route::post('dashboard',[HotelController::class, 'dashboard'])->name('hotel.dashboard');
});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){



});
