<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthServices;
use App\Services\ProfessionalServices;
use Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $loginRequest , AuthServices $authServices , ProfessionalServices $professionalServices) {
        $user = $authServices->existeEmail($loginRequest->email);

        if (!$user) {
            return response()->json(
                [
                    "success" => false,
                    "data" => [],
                    "message" => "Acune utilisateur existe avec cet email."
                ],
                401
            );
        }

        if (!Hash::check($loginRequest->password , $user->password)) {
            return response()->json(
                [
                    "success" => false,
                    "data" => [],
                    "message" => "Mot de passe est incorrect"
                ],
                401
            );
        }

        if (!$user->is_active) {
            return response()->json(
                [
                    "success" => false,
                    "data" => [],
                    "message" => "Votre compte a ete desactive. Merci de contacter l'administrateur."
                ],
                403
            );
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $professional = null;

        if ($user->role === 'professional') {
            $professional = $professionalServices->getProfessionalInfo($user->id);
        }


        if ($professional) {
            return response()->json([
                "success" => true,
                "data" => [
                    "user" => $user,
                    "professional" => $professional,
                    "token" => $token
                ],
                "message" => "Bonjour $user->first_name, votre compt professionnal creer en succe"
            ], 201);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "user" => $user,
                "token" => $token
            ],
            "message" => "Bonjour $user->first_name, votre compt creer en succe"
        ], 201);
    }
}
