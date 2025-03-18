<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {

        // Create Super Admin role with all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Create predefined roles
        $roles = ['Super Admin', 'SCM', 'Vendor', 'Management'];
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
        }
    }
}
