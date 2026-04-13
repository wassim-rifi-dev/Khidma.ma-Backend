<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
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

    Route::middleware('role:admin')->group(function() {});

    Route::middleware('role:client')->group(function() {});

    Route::middleware('role:professional')->group(function() {});
});
