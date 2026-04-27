<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AnalyticsService;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analyticsService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'analytics' => $analyticsService->getAnalyticsOverview(),
            ],
            'message' => 'Admin analytics retrieved successfully',
        ], 200);
    }
}
