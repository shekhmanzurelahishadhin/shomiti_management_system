<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
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

        // Member user — linked to first actual member record
        $firstMember = Member::orderBy('id')->first();
        $memberUser  = User::firstOrCreate(
            ['email' => 'member@nabadiganta.com'],
            [
                'name'      => $firstMember ? $firstMember->name : 'Member User',
                'password'  => Hash::make('password'),
                'status'    => 'active',
                'phone'     => '01700000003',
                'member_id' => $firstMember?->id,
            ]
        );
        $memberUser->assignRole('Member');

        // If member already existed but has no member_id, update it
        if (!$memberUser->member_id && $firstMember) {
            $memberUser->update(['member_id' => $firstMember->id]);
        }
    }
}
