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
        if ($authServices->existeEmail($registerRequest->email)) {
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

        return response()->json([
            "success" => true,
            "data" => [
                "user" => $user,
            ],
            "message" => "Bonjour $user->name, votre compt creer en succe"
        ], 201);
    }

    // public function register(RegisterRequest $registerRequest , AuthServices $authServices) {
    //         $user = $authServices->existeEmail($registerRequest->email);

    //         if ($user) {
    //             return response()->json(
    //                 [
    //                     "success" => false,
    //                     "data" => [],
    //                     "message" => "Il existe un utilisateur avec cet email"
    //                 ],
    //                 401
    //             );
    //         }

    //         $data = [
    //             'name' => $registerRequest->name,
    //             'email' => $registerRequest->email,
    //             'password' => Hash::make($registerRequest->password)
    //         ];
    //         $newUser = $authServices->createAccount($data);

    //         $token = $newUser->createToken('auth-token')->plainTextToken;

    //         return response()->json(
    //             [
    //                     "success" => true,
    //                     "data" => [
    //                         'user' => $newUser,
    //                         'token' => $token
    //                     ],
    //                     "message" => "Bonjour $newUser->name, votre compt creer en succe"
    //                 ] ,
    //                 201
    //         );
    //     }
}
