<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthServices;
use Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $registerRequest , AuthServices $authServices) {
        $existeUser = $authServices->existeEmail($registerRequest->email);
        if ($existeUser) {
            return response()->json(
                [
                    "success" => false,
                    "data" => [],
                    "message" => "Il existe un utilisateur avec cet email"
                ],
                401
            );
        }

        $data = [
            'name' => $registerRequest->name,
            'email' => $registerRequest->email,
            'phone' => $registerRequest->phone,
            'role' => $registerRequest->role,
            'password' => Hash::make($registerRequest->password),
        ];

        $user = $authServices->createNewUser($data);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            "success" => true,
            "data" => [
                "user" => $user,
                "token" => $token
            ],
            "message" => "Bonjour $user->name, votre compt creer en succe"
        ], 201);
    }
}
