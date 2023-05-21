<?php

use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('adm',function(){
    return 'here are admin routes';
});



Route::post('login',[AdminLoginController::class, 'adminLogin'])->name('doctor.login');

Route::group( ['prefix' => 'doctor','middleware' => ['auth:doctor-api','scopes:doctor'] ],function(){

    Route::post('dashboard',[AdminLoginController::class, 'doctorDashboard'])->name('doctor.dashboard');
});
