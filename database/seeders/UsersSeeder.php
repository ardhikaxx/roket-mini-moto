<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Budi Administrator',
                'pin' => Hash::make('2222'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true
            ]
        );

        User::firstOrCreate(['username' => 'kepala_sby'], [
            'name' => 'Agus Kepala SBY', 'pin' => Hash::make('2222'),
            'role' => 'kepala_toko', 'phone' => '081234567891', 'is_active' => true
        ]);

        User::firstOrCreate(['username' => 'kepala_mlg'], [
            'name' => 'Dewi Kepala MLG', 'pin' => Hash::make('2222'),
            'role' => 'kepala_toko', 'phone' => '081234567892', 'is_active' => true
        ]);

        for ($i = 1; $i <= 8; $i++) {
            User::firstOrCreate(
                ['username' => "karyawan$i"],
                [
                    'name' => "Karyawan Store $i",
                    'pin' => Hash::make('2222'),
                    'role' => 'karyawan',
                    'phone' => "0812000000$i",
                    'is_active' => true
                ]
            );
        }
    }
}
