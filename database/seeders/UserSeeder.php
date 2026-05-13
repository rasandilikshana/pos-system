<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'admin@pos.test',   'name' => 'Admin User',   'role' => 'admin'],
            ['email' => 'manager@pos.test', 'name' => 'Manager User', 'role' => 'manager'],
            ['email' => 'cashier@pos.test', 'name' => 'Cashier User', 'role' => 'cashier'],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
