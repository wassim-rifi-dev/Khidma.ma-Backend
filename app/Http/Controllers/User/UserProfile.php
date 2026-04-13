<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\updateUserRequest;
use Illuminate\Http\Request;

class UserProfile extends Controller
{
    public function update(updateUserRequest $updateUserRequest) {
        $user = $updateUserRequest->user();
        $data = $updateUserRequest->validated();

        $user->update($data);

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => $user->fresh(),
            ],
            'message' => 'Profile updated successfully',
        ], 200);
    }
}
