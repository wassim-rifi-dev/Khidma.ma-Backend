<?php

namespace App\Http\Controllers;

use App\Services\Service\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request, ServiceService $serviceService) {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 50) : 10;
        $filters = $request->only(['query', 'category', 'city', 'sort']);

        $services = $serviceService->getAllServices($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
                'cities' => $serviceService->getServiceCities(),
            ],
            'message' => "C'est sa les services"
        ], 200);
    }

    public function show(int $id, ServiceService $serviceService) {
        $service = $serviceService->getServiceDetailsById($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service,
            ],
            'message' => 'Service retrieved successfully'
        ], 200);
    }

}
