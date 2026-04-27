<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserStatusRequest;
use App\Services\Admin\UserManagementService;

class UserController extends Controller
{
    public function index(UserManagementService $userManagementService)
    {
        $users = $userManagementService->getAllUsers();

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users
            ],
            'message' => 'Users retrieved successfully'
        ], 200);
    }

    public function userCount(UserManagementService $userManagementService) {
        $users = $userManagementService->getAllUsers();

        return response()->json([
            'success' => true,
            'data' => [
                'user_total' => $users->count()
            ],
            'message' => 'Number of user'
        ], 200);
    }

    public function totalUsersCount(UserManagementService $userManagementService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $userManagementService->getTotalUsersCount(),
            ],
            'message' => 'Total users count retrieved successfully'
        ], 200);
    }

    public function activeUsersCount(UserManagementService $userManagementService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'active_accounts' => $userManagementService->getActiveUsersCount(),
            ],
            'message' => 'Active users count retrieved successfully'
        ], 200);
    }

    public function adminsCount(UserManagementService $userManagementService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'admins' => $userManagementService->getAdminsCount(),
            ],
            'message' => 'Admins count retrieved successfully'
        ], 200);
    }

    public function updateStatus(int $id, UpdateUserStatusRequest $request, UserManagementService $userManagementService)
    {
        $user = $userManagementService->getUserById($id);

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
                'message' => 'You cannot change your own account status'
            ], 422);
        }

        $updatedUser = $userManagementService->updateUserStatus(
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

    public function destroy(int $id, UserManagementService $userManagementService)
    {
        $user = $userManagementService->getUserById($id);

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

        $userManagementService->deleteUser($user);

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'User deleted successfully'
        ], 200);
    }
}
