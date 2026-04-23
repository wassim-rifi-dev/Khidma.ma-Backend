<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminUserManagementService;

class UserManagementController extends Controller
{
    public function index(AdminUserManagementService $adminUserManagementService)
    {
        $users = $adminUserManagementService->getAllUsers();

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users,
                'total' => $users->count(),
            ],
            'message' => 'Users retrieved successfully'
        ], 200);
    }
}
