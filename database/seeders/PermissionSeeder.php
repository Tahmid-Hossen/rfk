<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Permission Management
            'view permissions',


            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign permissions',

            // Requisitions
            'view requisitions',
            'create requisitions',
            'edit requisitions',
            'delete requisitions',
            'approve requisitions',

            // Request for Information (RFI)
            'view rfi',
            'create rfi',
            'edit rfi',
            'delete rfi',
            'respond to rfi',

            // Request for Quotation (RFQ)
            'view rfq',
            'create rfq',
            'edit rfq',
            'delete rfq',
            'approve rfq',

            // Price Quotations
            'view price quotations',
            'create price quotations',
            'edit price quotations',
            'delete price quotations',
            'approve price quotations',

            // Purchase Orders
            'view purchase orders',
            'create purchase orders',
            'edit purchase orders',
            'delete purchase orders',
            'approve purchase orders',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
