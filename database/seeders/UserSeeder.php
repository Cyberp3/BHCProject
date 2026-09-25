<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = [
            [
                'name' => 'Tacunan Health Admin',
                'email' => 'admin@tacunan.gov.ph',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'contact_number' => '09171234567',
                'is_active' => true,
            ],
            [
                'name' => 'Maria Santos, RN',
                'email' => 'nurse@tacunan.gov.ph',
                'password' => Hash::make('password123'),
                'role' => 'nurse',
                'contact_number' => '09181234567',
                'is_active' => true,
            ],
            [
                'name' => 'Elena Reyes, RMW',
                'email' => 'midwife@tacunan.gov.ph',
                'password' => Hash::make('password123'),
                'role' => 'midwife',
                'contact_number' => '09191234567',
                'is_active' => true,
            ],
            [
                'name' => 'Juana Dela Cruz (BNS)',
                'email' => 'bns@tacunan.gov.ph',
                'password' => Hash::make('password123'),
                'role' => 'bns',
                'contact_number' => '09201234567',
                'is_active' => true,
            ],
            [
                'name' => 'Rosa Flores (BHW)',
                'email' => 'bhw@tacunan.gov.ph',
                'password' => Hash::make('password123'),
                'role' => 'bhw',
                'contact_number' => '09211234567',
                'is_active' => true,
            ],
            [
                'name' => 'Carmen Lim (BHV)',
                'email' => 'bhv@tacunan.gov.ph',
                'password' => Hash::make('password123'),
                'role' => 'bhv',
                'contact_number' => '09221234567',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
