<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class AdminUserManagementService
{
    public function getAllUsers(): Collection
    {
        return User::with('professional')
            ->latest()
            ->get();
    }

    public function getUserById(int $id): ?User
    {
        return User::with('professional')->find($id);
    }

    public function updateUserStatus(User $user, bool $isActive): User
    {
        $user->update([
            'is_active' => $isActive,
        ]);

        return $user->fresh(['professional']);
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }
}
