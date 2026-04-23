<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Services\AdminUserManagementService;

class UserManagementController extends Controller
{
    public function index(AdminUserManagementService $adminUserManagementService)
    {
        $users = $adminUserManagementService->getAllUsers();

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users
            ],
            'message' => 'Users retrieved successfully'
        ], 200);
    }

    public function userCount(AdminUserManagementService $adminUserManagementService) {
        $users = $adminUserManagementService->getAllUsers();

        return response()->json([
            'success' => true,
            'data' => [
                'user_total' => $users->count()
            ],
            'message' => 'Number of user'
        ], 200);
    }

    public function updateStatus(int $id, UpdateUserStatusRequest $request, AdminUserManagementService $adminUserManagementService)
    {
        $user = $adminUserManagementService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'User not found'
            ], 404);
        }

        $updatedUser = $adminUserManagementService->updateUserStatus(
            $user,
            (bool) $request->validated()['is_active']
        );

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $updatedUser,
            ],
            'message' => 'User status updated successfully'
        ], 200);
    }

    public function destroy(int $id, AdminUserManagementService $adminUserManagementService)
    {
        $user = $adminUserManagementService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'User not found'
            ], 404);
        }

        if ((int) request()->user()->id === (int) $user->id) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        $adminUserManagementService->deleteUser($user);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'User deleted successfully'
        ], 200);
    }
}
