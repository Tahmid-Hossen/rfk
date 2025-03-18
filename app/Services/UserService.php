<?php

namespace App\Services;

class UserService
{
    /**
     * Get all users data
     */
    public function getFormattedData($users)
    {
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames(), // This should return the roles the user has
                'permissions' => $user->getAllPermissions()->pluck('name'), // Ensure you're using getAllPermissions() instead of getPermissionNames() if needed
            ];
        });
    }

    /**
     * Get all users data
     */
    public function getFormattedSingleData($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames(), // This should return the roles the user has
            'permissions' => $user->getAllPermissions()->pluck('name'), // Ensure you're using getAllPermissions() instead of getPermissionNames() if needed
        ];
    }




}
