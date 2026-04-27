<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Collection;

class UserManagementService
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

    public function getTotalUsersCount(): int
    {
        return User::count();
    }

    public function getActiveUsersCount(): int
    {
        return User::where('is_active', true)->count();
    }

    public function getAdminsCount(): int
    {
        return User::where('role', 'admin')->count();
    }

    public function updateUserStatus(User $user, bool $isActive): User
    {
        $user->update([
            'is_active' => $isActive,
        ]);

        if (!$isActive) {
            $user->tokens()->delete();
        }

        return $user->fresh(['professional']);
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }
}
