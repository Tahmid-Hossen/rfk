<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the RoleSeeder first to create roles and permissions
        $this->call(RoleSeeder::class);

        // Create a Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Change this to your desired admin email
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'), // Change the password for security
            ]
        );

        // Assign the Super Admin role
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $superAdmin->assignRole($role);
        }
    }
}
