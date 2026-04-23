<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ServiceServices;

class ServiceManagementController extends Controller
{
    public function index(ServiceServices $serviceServices)
    {
        $services = $serviceServices->getAllServicesForAdmin();

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
                'total' => $services->count(),
            ],
            'message' => 'Services retrieved successfully'
        ], 200);
    }
}
