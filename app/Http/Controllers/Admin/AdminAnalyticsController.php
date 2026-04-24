<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;

class AdminAnalyticsController extends Controller
{
    public function index(AdminAnalyticsService $adminAnalyticsService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'analytics' => $adminAnalyticsService->getAnalyticsOverview(),
            ],
            'message' => 'Admin analytics retrieved successfully',
        ], 200);
    }
}
