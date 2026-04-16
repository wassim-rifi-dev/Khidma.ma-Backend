<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Services\ProfessionalServices;
use App\Services\ServiceServices;

class ServiceController extends Controller
{
    public function store(StoreServiceRequest $request, ServiceServices $serviceServices, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalInfo((int) $request->user()->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found'
            ], 404);
        }

        $category = $serviceServices->getCategoryByName($professional->category);

        if (!$category) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Category not found'
            ], 404);
        }

        $data = array_merge($request->validated(), [
            'professional_id' => $professional->id,
            'categorie_id' => $category->id,
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
}
