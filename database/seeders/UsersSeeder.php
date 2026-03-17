<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@nabadiganta.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'status' => 'active', 'phone' => '01700000000']
        );
        $superAdmin->assignRole('Super Admin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@nabadiganta.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password'), 'status' => 'active', 'phone' => '01700000001']
        );
        $admin->assignRole('Admin');

        $treasurer = User::firstOrCreate(
            ['email' => 'treasurer@nabadiganta.com'],
            ['name' => 'Treasurer User', 'password' => Hash::make('password'), 'status' => 'active', 'phone' => '01700000002']
        );
        $treasurer->assignRole('Treasurer');

        $member = User::firstOrCreate(
            ['email' => 'member@nabadiganta.com'],
            ['name' => 'Member User', 'password' => Hash::make('password'), 'status' => 'active', 'phone' => '01700000003']
        );
        $member->assignRole('Member');
    }
}
