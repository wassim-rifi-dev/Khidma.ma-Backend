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

    public function publishedCount(ServiceServices $serviceServices)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'published_services' => $serviceServices->getPublishedServicesCount(),
            ],
            'message' => 'Number of published services'
        ], 200);
    }

    public function destroy(int $id, ServiceServices $serviceServices)
    {
        $service = $serviceServices->getServiceById($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Service not found'
            ], 404);
        }

        $serviceServices->deleteService($service);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Service deleted successfully'
        ], 200);
    }
}
