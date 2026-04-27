<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Mail\Service\ServiceCreatedMail;
use App\Services\Professional\ProfessionalService;
use App\Services\Service\ServiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProfessionalServiceController extends Controller
{
    public function index(Request $request, ServiceService $serviceService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $services = $serviceService->getServicesByProfessional($professional->id);

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
            ],
            'message' => 'Professional services retrieved successfully'
        ], 200);
    }

    public function summary(Request $request, ServiceService $serviceService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $summary = $serviceService->getProfessionalServicesSummary($professional->id);

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
            ],
            'message' => 'Professional services summary retrieved successfully'
        ], 200);
    }

    public function trashed(ServiceService $serviceService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $services = $serviceService->getDeletedServicesByProfessional($professional->id);

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
            ],
            'message' => 'Deleted services retrieved successfully'
        ], 200);
    }

    public function store(StoreServiceRequest $request, ServiceService $serviceService, ProfessionalService $professionalService)
    {
        $user = $request->user();
        $professional = $professionalService->getProfessionalInfo((int) $user->id);

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

        unset($data['cover_image'], $data['gallery_images']);

        $service = $serviceService->createServices(
            $data,
            $request->file('cover_image'),
            $request->file('gallery_images', [])
        );

        Mail::to($user->email)->send(new ServiceCreatedMail($service));

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $service,
            ],
            'message' => 'Service created successfully'
        ], 201);
    }

    public function update(UpdateServiceRequest $request, int $id, ServiceService $serviceService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $service = $serviceService->getServiceById($id);

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

        $updatedService = $serviceService->updateService($service, $request->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $updatedService,
            ],
            'message' => 'Service updated successfully'
        ], 200);
    }

    public function destroy(int $id, ServiceService $serviceService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $service = $serviceService->getServiceById($id);

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

        $serviceService->deleteService($service);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Service deleted successfully'
        ], 200);
    }

    public function restore(int $id, ServiceService $serviceService, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalInfo((int) request()->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $service = $serviceService->getDeletedServiceById($id);

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

        $restoredService = $serviceService->restoreService($service);

        return response()->json([
            'success' => true,
            'data' => [
                'service' => $restoredService,
            ],
            'message' => 'Service restored successfully'
        ], 200);
    }
}
