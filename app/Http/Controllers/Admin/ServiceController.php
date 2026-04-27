<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Service\ServiceService;

class ServiceController extends Controller
{
    public function index(ServiceService $serviceService)
    {
        $services = $serviceService->getAllServicesForAdmin();

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
                'total' => $services->count(),
            ],
            'message' => 'Services retrieved successfully'
        ], 200);
    }

    public function publishedCount(ServiceService $serviceService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'published_services' => $serviceService->getPublishedServicesCount(),
            ],
            'message' => 'Number of published services'
        ], 200);
    }

    public function destroy(int $id, ServiceService $serviceService)
    {
        $service = $serviceService->getServiceById($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Service not found'
            ], 404);
        }

        $serviceService->deleteService($service);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Service deleted successfully'
        ], 200);
    }
}
