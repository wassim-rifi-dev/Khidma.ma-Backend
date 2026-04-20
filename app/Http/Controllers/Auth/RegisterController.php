<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\RegisterMAil;
use App\Services\AuthServices;
use App\Services\ProfessionalServices;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function register(RegisterRequest $registerRequest , AuthServices $authServices, ProfessionalServices $professionalServices) {
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
            'first_name' => $registerRequest->first_name,
            'last_name' => $registerRequest->last_name,
            'username' => $registerRequest->username,
            'email' => $registerRequest->email,
            'phone' => $registerRequest->phone,
            'role' => $registerRequest->role,
            'password' => Hash::make($registerRequest->password),
        ];

        $user = $authServices->createNewUser($data);

        if ($user->role === 'professional') {
            $category = $professionalServices->getCategoryByName($registerRequest->category);

            if (!$category) {
                return response()->json([
                    "success" => false,
                    "data" => [],
                    "message" => "Category not found"
                ], 404);
            }

            $professionalData = [
                'user_id' => $user->id,
                'categorie_id' => $category->id,
                'city' => $registerRequest->city
            ];

            $professional = $professionalServices->create($professionalData);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        Mail::to($user->email)->send(new RegisterMAil($user));

        if ($user->role === 'professional') {
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
