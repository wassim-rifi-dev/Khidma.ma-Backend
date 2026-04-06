<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(
            [
                "success" => true,
                "data" => [],
                "message" => "Logout en succe"
            ],
            200
        );
    }
}
