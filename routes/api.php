<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\ProfessionalManagementController;
use App\Http\Controllers\Admin\ServiceManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\Professional\ProfessionalProfile;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\User\UserProfile;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/test', function () {
    return response()->json([
        'success' => true,
    ]);
});

// Categories
Route::get('categories' , [CategoryController::class , 'index']);

// Authentication
Route::post('register' , [RegisterController::class , 'register']); // Register
Route::post('login' , [LoginController::class , 'login']); // Login

Route::middleware('auth:sanctum')->group(function() {
    // Authentication
    Route::post('logout' , [LogoutController::class , 'logout']); // Logout

    // Shared authenticated routes
    Route::get('user/profile' , [UserProfile::class , 'show']); // show user profile
    Route::put('user/profile/update' , [UserProfile::class , 'update']); // update user profile
    Route::post('user/profile/update' , [UserProfile::class , 'update']); // update user profile with multipart form-data
    Route::get('professionals/top' , [ProfessionalProfile::class , 'top']); // show top professionals
    Route::get('professionals/{professionalId}' , [ProfessionalProfile::class , 'showById']); // show selected professional profile

    Route::get('services' , [ServiceController::class , 'index']); // showw all service
    Route::get('services/{id}' , [ServiceController::class , 'show']); // show selected service

    // Chat & messages
    Route::get('chat/{chatId}' , [ChatController::class , 'index']); // show chat messages
    Route::post('message/store/{chatId}' , [MessagesController::class , 'store']); // create message

    // Admin
    Route::middleware('role:admin')->group(function() {
        Route::get('admin/users', [UserManagementController::class, 'index']); // show all users for admin
        Route::patch('admin/users/{id}/status', [UserManagementController::class, 'updateStatus']); // update user status for admin
        Route::delete('admin/users/{id}', [UserManagementController::class, 'destroy']); // delete user for admin
        Route::get('admin/professionals', [ProfessionalManagementController::class, 'index']); // show all professionals for admin
        Route::get('admin/services', [ServiceManagementController::class, 'index']); // show all services for admin
        Route::get('admin/categories', [CategoryManagementController::class, 'index']); // show all categories for admin
        Route::post('admin/category/store', [CategoryManagementController::class, 'store']); // create category for admin
        Route::put('admin/category/update/{id}', [CategoryManagementController::class, 'update']); // update category for admin
        Route::delete('admin/category/delete/{id}', [CategoryManagementController::class, 'destroy']); // delete category for admin
    });

    // Client
    Route::middleware('role:client,professional')->group(function() {
        Route::get('client/request' , [RequestController::class , 'clientRequest']); // show client requests
        Route::get('client/request/count' , [RequestController::class , 'clientRequestsCount']); // get client requests count
        Route::get('client/request/count/completed' , [RequestController::class , 'completedClientRequestsCount']); // get completed client requests count
        Route::get('client/review/count' , [ReviewsController::class , 'clientReviewsCount']); // get client reviews count
        Route::get('client/request/latest' , [RequestController::class , 'lastThreeClientRequest']); // get Last three client request
        Route::get('client/request/latest-six' , [RequestController::class , 'lastSixClientRequest']); // get last six client request
        Route::get('review/service/{serviceId}' , [ReviewsController::class , 'index']); // show service reviews
        Route::post('request/store/{serviceId}' , [RequestController::class , 'store']); // create request
        Route::put('request/cancel/{id}' , [RequestController::class , 'cancel']); // cancel new request
        Route::post('review/store/{orderId}' , [ReviewsController::class , 'store']); // create review
    });

    // Professional
    Route::middleware('role:professional')->group(function() {
        // Professional profile
        Route::get('profissional/profile' , [ProfessionalProfile::class , 'show']); // show profissional profile
        Route::get('professional/analytics' , [ProfessionalProfile::class , 'analytics']); // show professional analytics
        Route::put('professional/profile/update' , [ProfessionalProfile::class , 'update']); // update profissional profile

        // Professional requests
        Route::get('professional/request' , [RequestController::class , 'professionalRequest']); // show professional requests
        Route::get('professional/request/{id}' , [RequestController::class , 'professionalRequestDetails']); // show professional request details
        Route::put('request/update-status/{id}' , [RequestController::class , 'updateStatus']); // update request status

        // Professional services
        Route::get('professional/services' , [ServiceController::class , 'professionalServices']); // show authenticated professional services
        Route::get('professional/services/summary' , [ServiceController::class , 'professionalServicesSummary']); // show authenticated professional services summary
        Route::get('service/trashed' , [ServiceController::class , 'trashed']); // show deleted services
        Route::post('service/store' , [ServiceController::class , 'store']); // create service
        Route::put('service/update/{id}' , [ServiceController::class , 'update']); // update service
        Route::delete('service/delete/{id}' , [ServiceController::class , 'destroy']); // delete service
        Route::put('service/restore/{id}' , [ServiceController::class , 'restore']); // restore service
    });
});
