<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage users',
            'manage members',
            'manage committees',
            'generate bills',
            'collect payments',
            'manage expenses',
            'view reports',
            'manage settings',
            // Investment permissions
            'submit investment request',   // Members
            'manage investment agenda',    // Admin/Secretary
            'approve investments',         // Parliamentary/Super Admin
            'process investment payment',  // Accounts/Treasurer
            'settle investments',          // Accounts/Treasurer
            'view investments',            // All roles
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'manage members', 'manage committees', 'generate bills',
            'collect payments', 'manage expenses', 'view reports',
            'submit investment request', 'manage investment agenda',
            'approve investments', 'process investment payment',
            'settle investments', 'view investments',
        ]);

        $treasurer = Role::firstOrCreate(['name' => 'Treasurer']);
        $treasurer->givePermissionTo([
            'generate bills', 'collect payments', 'manage expenses', 'view reports',
            'process investment payment', 'settle investments', 'view investments',
        ]);

        $member = Role::firstOrCreate(['name' => 'Member']);
        $member->givePermissionTo([
            'view reports', 'submit investment request', 'view investments',
        ]);
    }
}
