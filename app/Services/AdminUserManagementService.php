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
}
