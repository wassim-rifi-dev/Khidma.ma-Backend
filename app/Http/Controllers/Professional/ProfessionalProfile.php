<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\updateUserRequest;
use App\Services\ProfessionalServices;
use Illuminate\Http\Request;

class ProfessionalProfile extends Controller
{
    public function show(Request $request , ProfessionalServices $professionalServices) {
        $user = $request->user();
        $professional = $professionalServices->getProfessionalInfo((int) $user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'professional' => $professional
            ]
        ] , 200);
    }
}
