<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'user-create', 'user-edit', 'user-delete', 'user-show',
            'role-create', 'role-edit', 'role-delete', 'role-show'
        ];

        // Create each permission
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Super Admin role with all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Create predefined roles
        $roles = ['Super Admin', 'SCM', 'Vendor', 'Management'];
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            // Assign specific permissions if needed (modify as required)
            $role->givePermissionTo(['user-show']);
        }
    }
}
