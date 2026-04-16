<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Services\ProfessionalServices;
use App\Services\ServiceServices;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request, ServiceServices $serviceServices) {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 50) : 10;

        $services = $serviceServices->getAllServices($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
            ],
            'message' => `C'est sa les services`
        ], 201);
    }

    public function trashed(ServiceServices $serviceServices, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $services = $serviceServices->getDeletedServicesByProfessional($professional->id);

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
            ],
            'message' => 'Deleted services retrieved successfully'
        ], 200);
    }

    public function store(StoreServiceRequest $request, ServiceServices $serviceServices, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $data = array_merge($request->validated(), [
            'professional_id' => $professional->id,
            'categorie_id' => $professional->categorie_id,
        ]);

        $service = $serviceServices->createServices($data);

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service,
            ],
            'message' => 'Service created successfully'
        ], 201);
    }

    public function update(UpdateServiceRequest $request, int $id, ServiceServices $serviceServices, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $service = $serviceServices->getServiceById($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Service not found'
            ], 404);
        }

        if ($service->professional_id !== $professional->id) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Unauthorized'
            ], 403);
        }

        $updatedService = $serviceServices->updateService($service, $request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $updatedService,
            ],
            'message' => 'Service updated successfully'
        ], 200);
    }

    public function destroy(int $id, ServiceServices $serviceServices, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $service = $serviceServices->getServiceById($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Service not found'
            ], 404);
        }

        if ($service->professional_id !== $professional->id) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Unauthorized'
            ], 403);
        }

        $serviceServices->deleteService($service);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Service deleted successfully'
        ], 200);
    }

    public function restore(int $id, ServiceServices $serviceServices, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $service = $serviceServices->getDeletedServiceById($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Deleted service not found'
            ], 404);
        }

        if ($service->professional_id !== $professional->id) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Unauthorized'
            ], 403);
        }

        $restoredService = $serviceServices->restoreService($service);

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $restoredService,
            ],
            'message' => 'Service restored successfully'
        ], 200);
    }
}
