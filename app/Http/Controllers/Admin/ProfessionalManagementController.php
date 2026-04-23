<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
