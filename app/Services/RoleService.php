<?php

namespace App\Services;

class RoleService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /*
     * Get single role data
     * */
    public function formattedSingleData($role){
        return [
            'id' => $role->id, // Role name
            'name' => $role->name, // Role name
            'permissions' => $role->permissions->pluck('name')->toArray(), // List of permission names associated with the role
        ];
    }

    /*
     * Get formatted role data
     * */
    public function formattedData($roles){
        return $roles->map(function ($role) {
            return [
                'id' => $role->id, // Role name
                'name' => $role->name, // Role name
                'permissions' => $role->permissions->pluck('name')->toArray(), // List of permission names associated with the role
            ];
        });
    }
}
