<?php

//use App\Http\Controllers\HotelController;
use App\Http\Controllers\Hotel\AdminController;
use App\Http\Controllers\Hotel\UserController;
use App\Http\Controllers\Hotel\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Hotel Admin Routes:
Route::post('hotel/adminlogin',[AdminController::class, 'AdminLogin']);
Route::post('admin/createhotel',[AdminController::class, 'CreateHotel']);
Route::post('admin/addRoom',[AdminController::class, 'addRooms']);
Route::post('admin/addPhoto',[AdminController::class, 'addPhotos']);
Route::post('admin/addRoomPhoto',[AdminController::class, 'addRoomPhotos']);

//Hotel user Routes:
Route::post('user/addReview',[UserController::class, 'addReview']);
Route::get('TopRatedAndTypes',[HotelController::class, 'TopRatedAndTypes']);
Route::get('TopRated',[HotelController::class, 'TopRated']);
Route::get('ShowHotelTypes',[HotelController::class, 'ShowHotelTypes']);
Route::get('ShowRoomsTypes',[HotelController::class, 'ShowRoomsTypes']);
Route::get('ShowAllHotel',[HotelController::class, 'ShowALLHotel']);
Route::post('ShowHotelRooms',[HotelController::class, 'ShowHotelRooms']);
Route::post('hotel/Hotelsearch',[UserController::class, 'Hotelsearch']);


// trial root:
Route::post('getReservations',[UserController::class,'Reservations']);



// this is mohamad code:
//Route::post('get')





Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){

    Route::post('dashboard',[HotelController::class, 'dashboard'])->name('hotel.dashboard');
});

Route::group( ['prefix' => 'hotel','middleware' => ['auth:hotel-api'] ],function(){



});
