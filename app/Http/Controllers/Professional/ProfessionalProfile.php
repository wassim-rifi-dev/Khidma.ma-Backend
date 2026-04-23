<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professional\updateProfissionalProfile;
use App\Http\Requests\User\updateUserRequest;
use App\Services\ProfessionalServices;
use Illuminate\Http\Request;

class ProfessionalProfile extends Controller
{
    public function top(ProfessionalServices $professionalServices) {
        $professionals = $professionalServices->getTopProfessionals(2);

        return response()->json([
            'success' => true,
            'data' => [
                'professionals' => $professionals
            ],
            'message' => 'Top professionals retrieved successfully'
        ], 200);
    }

    public function show(Request $request , ProfessionalServices $professionalServices) {
        $user = $request->user();
        $professional = $professionalServices->getProfessionalDashboardProfile((int) $user->id);

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

    public function showById(int $professionalId, ProfessionalServices $professionalServices) {
        $professional = $professionalServices->getProfessionalProfileById($professionalId);

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

    public function analytics(Request $request, ProfessionalServices $professionalServices) {
        $analytics = $professionalServices->getProfessionalAnalytics((int) $request->user()->id);

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

    public function update(updateProfissionalProfile $request , ProfessionalServices $professionalServices) {
        $user = $request->user();

        $professional = $professionalServices->getProfessionalInfo((int) $user->id);

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
