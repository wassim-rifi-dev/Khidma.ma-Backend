<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfessionalVerifyRequest;
use App\Services\ProfessionalServices;

class ProfessionalManagementController extends Controller
{
    public function index(ProfessionalServices $professionalServices)
    {
        $professionals = $professionalServices->getAllProfessionals();

        return response()->json([
            'success' => true,
            'data' => [
                'professionals' => $professionals,
                'total' => $professionals->count(),
            ],
            'message' => 'Professionals retrieved successfully'
        ], 200);
    }

    public function updateVerification(int $id, UpdateProfessionalVerifyRequest $request, ProfessionalServices $professionalServices)
    {
        $professional = $professionalServices->getProfessionalById($id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional not found'
            ], 404);
        }

        $updatedProfessional = $professionalServices->updateProfessionalVerification(
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
