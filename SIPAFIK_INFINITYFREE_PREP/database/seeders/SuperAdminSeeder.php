<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // firstOrCreate: tidak me-reset password Super Admin yang sudah ada.
        User::firstOrCreate(
            ['email' => 'superadmin@sipafik.test'],
            [
                'name' => 'Super Admin SIPAFIK',
                'username' => 'superadmin',
                'password' => Hash::make(env('SIPAFIK_SUPERADMIN_PASSWORD')),
                'role' => 'super_admin',
                'campus_id' => null,
                'is_active' => true,
            ]
        );
    }
}