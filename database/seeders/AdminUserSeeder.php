<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@shivibes.test'],
            [
                'name' => 'Shivibes Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->forceFill([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ])->save();
    }
}
