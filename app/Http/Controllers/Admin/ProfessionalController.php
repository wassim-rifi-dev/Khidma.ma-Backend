<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professional\UpdateProfessionalVerifyRequest;
use App\Services\Professional\ProfessionalService;

class ProfessionalController extends Controller
{
    public function index(ProfessionalService $professionalService)
    {
        $professionals = $professionalService->getAllProfessionals();

        return response()->json([
            'success' => true,
            'data' => [
                'professionals' => $professionals,
                'total' => $professionals->count(),
            ],
            'message' => 'Professionals retrieved successfully'
        ], 200);
    }

    public function verifiedCount(ProfessionalService $professionalService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'active_professionals' => $professionalService->getVerifiedProfessionalsCount(),
            ],
            'message' => 'Number of active professionals'
        ], 200);
    }

    public function updateVerification(int $id, UpdateProfessionalVerifyRequest $request, ProfessionalService $professionalService)
    {
        $professional = $professionalService->getProfessionalById($id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional not found'
            ], 404);
        }

        $updatedProfessional = $professionalService->updateProfessionalVerification(
            $professional,
            (bool) $request->validated()['is_verified']
        );

        return response()->json([
            'success' => true,
            'data' => [
                'professional' => $updatedProfessional,
            ],
            'message' => 'Professional verification updated successfully'
        ], 200);
    }
}
