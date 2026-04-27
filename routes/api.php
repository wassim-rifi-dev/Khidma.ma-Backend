<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProfessionalController as AdminProfessionalController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Professional\ProfessionalProfileController;
use App\Http\Controllers\Professional\ProfessionalServiceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\User\RequestController;
use App\Http\Controllers\User\UserProfileController;
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

Route::middleware(['auth:sanctum', 'active'])->group(function() {
    // Authentication
    Route::post('logout' , [LogoutController::class , 'logout']); // Logout

    // Shared authenticated routes
    Route::get('user/profile' , [UserProfileController::class , 'show']); // show user profile
    Route::put('user/profile/update' , [UserProfileController::class , 'update']); // update user profile
    Route::post('user/profile/update' , [UserProfileController::class , 'update']); // update user profile with multipart form-data
    Route::get('professionals/top' , [ProfessionalProfileController::class , 'top']); // show top professionals
    Route::get('professionals/{professionalId}' , [ProfessionalProfileController::class , 'showById']); // show selected professional profile

    Route::get('services' , [ServiceController::class , 'index']); // showw all service
    Route::get('services/{id}' , [ServiceController::class , 'show']); // show selected service

    // Chat & messages
    Route::get('chats' , [ChatController::class , 'conversations']); // show user conversations
    Route::post('chat/direct/{professionalId}' , [ChatController::class , 'direct']); // create or get direct chat
    Route::get('chat/{chatId}' , [ChatController::class , 'index']); // show chat messages
    Route::post('message/store/{chatId}' , [MessageController::class , 'store']); // create message

    // Admin
    Route::middleware('role:admin')->group(function() {
        Route::get('admin/users', [AdminUserController::class, 'index']); // show all users for admin
        Route::get('admin/users/count', [AdminUserController::class, 'userCount']); // show user number
        Route::get('admin/users/count/total', [AdminUserController::class, 'totalUsersCount']); // show total users number
        Route::get('admin/users/count/active', [AdminUserController::class, 'activeUsersCount']); // show active users number
        Route::get('admin/users/count/admins', [AdminUserController::class, 'adminsCount']); // show admins number
        Route::patch('admin/users/{id}/status', [AdminUserController::class, 'updateStatus']); // update user status for admin
        Route::delete('admin/users/{id}', [AdminUserController::class, 'destroy']); // delete user for admin

        Route::get('admin/professionals', [AdminProfessionalController::class, 'index']); // show all professionals for admin
        Route::get('admin/professionals/count', [AdminProfessionalController::class, 'verifiedCount']); // show verified professionals number
        Route::patch('admin/professionals/{id}/verify', [AdminProfessionalController::class, 'updateVerification']); // update professional verification for admin

        Route::get('admin/services', [AdminServiceController::class, 'index']); // show all services for admin
        Route::get('admin/services/count', [AdminServiceController::class, 'publishedCount']); // show published services number
        Route::get('admin/requests/count/open', [RequestController::class, 'adminOpenRequestsCount']); // show open requests number
        Route::get('admin/requests/latest', [RequestController::class, 'adminLatestRequests']); // show latest requests for admin
        Route::get('admin/analytics', [AnalyticsController::class, 'index']); // show admin analytics overview
        Route::delete('admin/services/{id}', [AdminServiceController::class, 'destroy']); // delete service for admin

        Route::get('admin/categories', [AdminCategoryController::class, 'index']); // show all categories for admin
        Route::post('admin/category/store', [AdminCategoryController::class, 'store']); // create category for admin
        Route::put('admin/category/update/{id}', [AdminCategoryController::class, 'update']); // update category for admin
        Route::delete('admin/category/delete/{id}', [AdminCategoryController::class, 'destroy']); // delete category for admin
    });

    // Client
    Route::middleware('role:client,professional')->group(function() {
        Route::get('client/request' , [RequestController::class , 'clientRequest']); // show client requests
        Route::get('client/request/count' , [RequestController::class , 'clientRequestsCount']); // get client requests count
        Route::get('client/request/count/completed' , [RequestController::class , 'completedClientRequestsCount']); // get completed client requests count
        Route::get('client/review/count' , [ReviewController::class , 'clientReviewsCount']); // get client reviews count
        Route::get('client/request/latest' , [RequestController::class , 'lastThreeClientRequest']); // get Last three client request
        Route::get('client/request/latest-six' , [RequestController::class , 'lastSixClientRequest']); // get last six client request
        Route::get('review/service/{serviceId}' , [ReviewController::class , 'index']); // show service reviews
        Route::post('request/store/{serviceId}' , [RequestController::class , 'store']); // create request
        Route::put('request/cancel/{id}' , [RequestController::class , 'cancel']); // cancel new request
        Route::post('review/store/{orderId}' , [ReviewController::class , 'store']); // create review
    });

    // Professional
    Route::middleware('role:professional')->group(function() {
        // Professional profile
        Route::get('profissional/profile' , [ProfessionalProfileController::class , 'show']); // show profissional profile
        Route::get('professional/analytics' , [ProfessionalProfileController::class , 'analytics']); // show professional analytics
        Route::put('professional/profile/update' , [ProfessionalProfileController::class , 'update']); // update profissional profile

        // Professional requests
        Route::get('professional/request' , [RequestController::class , 'professionalRequest']); // show professional requests
        Route::get('professional/request/{id}' , [RequestController::class , 'professionalRequestDetails']); // show professional request details
        Route::put('request/update-status/{id}' , [RequestController::class , 'updateStatus']); // update request status

        // Professional services
        Route::get('professional/services' , [ProfessionalServiceController::class , 'index']); // show authenticated professional services
        Route::get('professional/services/summary' , [ProfessionalServiceController::class , 'summary']); // show authenticated professional services summary
        Route::get('service/trashed' , [ProfessionalServiceController::class , 'trashed']); // show deleted services
        Route::post('service/store' , [ProfessionalServiceController::class , 'store']); // create service
        Route::put('service/update/{id}' , [ProfessionalServiceController::class , 'update']); // update service
        Route::delete('service/delete/{id}' , [ProfessionalServiceController::class , 'destroy']); // delete service
        Route::put('service/restore/{id}' , [ProfessionalServiceController::class , 'restore']); // restore service
    });
});
