<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Professional\ProfessionalProfile;
use App\Http\Controllers\User\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'success' => true,
    ]);
});

// Auth
Route::post('register' , [RegisterController::class , 'register']); // Register
Route::post('login' , [LoginController::class , 'login']); // Login

Route::middleware('auth:sanctum')->group(function() {
    // Auth
    Route::post('logout' , [LogoutController::class , 'logout']); // Logout

    Route::put('user/profile/update' , [UserProfile::class , 'update']); // update user profile

    Route::middleware('role:admin')->group(function() {});

    Route::middleware('role:client')->group(function() {});

    Route::middleware('role:professional')->group(function() {
        Route::get('profissional/profile' , [ProfessionalProfile::class , 'show']); // show profissional profile
    });
});
