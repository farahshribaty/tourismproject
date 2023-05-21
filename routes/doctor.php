<?php

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

Route::post('doctor/login',[DoctorLoginController::class, 'doctorLogin'])->name('doctor.login');

Route::group( ['prefix' => 'doctor','middleware' => ['auth:doctor-api'] ],function(){

    Route::post('dashboard',[DoctorLoginController::class, 'doctorDashboard'])->name('doctor.dashboard');
});
