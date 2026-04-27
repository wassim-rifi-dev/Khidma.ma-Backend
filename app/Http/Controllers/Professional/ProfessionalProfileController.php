<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professional\UpdateProfessionalProfileRequest;
use App\Services\Professional\ProfessionalService;
use Illuminate\Http\Request;

class ProfessionalProfileController extends Controller
{
    public function top(ProfessionalService $professionalService) {
        $professionals = $professionalService->getTopProfessionals(2);

        return response()->json([
            'success' => true,
            'data' => [
                'professionals' => $professionals
            ],
            'message' => 'Top professionals retrieved successfully'
        ], 200);
    }

    public function show(Request $request , ProfessionalService $professionalService) {
        $user = $request->user();
        $professional = $professionalService->getProfessionalDashboardProfile((int) $user->id);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'professional' => $professional
            ]
        ] , 200);
    }

    public function showById(int $professionalId, ProfessionalService $professionalService) {
        $professional = $professionalService->getProfessionalProfileById($professionalId);

        if (!$professional) {
            return response()->json([
                'success' => false,
                'message' => 'Professional not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'professional' => $professional,
            ],
            'message' => 'Professional profile retrieved successfully',
        ], 200);
    }

    public function analytics(Request $request, ProfessionalService $professionalService) {
        $analytics = $professionalService->getProfessionalAnalytics((int) $request->user()->id);

        if (!$analytics) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Professional profile not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'analytics' => $analytics,
            ],
            'message' => 'Professional analytics retrieved successfully',
        ], 200);
    }

    public function update(UpdateProfessionalProfileRequest $request , ProfessionalService $professionalService) {
        $user = $request->user();

        $professional = $professionalService->getProfessionalInfo((int) $user->id);

        $professional->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Professional profile updated successfully',
            'data'    => [
                'professional' => $professional->fresh(),
            ],
        ], 200);
    }
}
