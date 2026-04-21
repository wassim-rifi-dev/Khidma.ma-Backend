<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\updateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Controller
{
    public function show(Request $request) {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => "Vous n'avez pas login"
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
            ],
            'message' => "C'est sa vos info"
        ], 200);
    }

    public function update(updateUserRequest $updateUserRequest) {
        $user = $updateUserRequest->user();
        $data = $updateUserRequest->validated();

        $removePhoto = filter_var($updateUserRequest->input('remove_photo', false), FILTER_VALIDATE_BOOLEAN);

        if ($removePhoto && $user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
            $data['photo'] = null;
        }

        if ($updateUserRequest->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $updateUserRequest->file('photo')->store('users/profile', 'public');
            $data['photo'] = $path;
        }

        unset($data['remove_photo']);

        $user->update($data);

        $freshUser = $user->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data'    => [
                'user' => array_merge($freshUser->toArray(), [
                    'photo' => $freshUser->photo
                        ? asset('storage/' . $freshUser->photo)
                        : null,
                ]),
            ],
        ], 200);
    }
}
