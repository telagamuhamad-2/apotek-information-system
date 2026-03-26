<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default owner
        $owner = User::firstOrCreate(
            ['email' => 'owner@apotek.com'],
            [
                'name' => 'Owner Apotek',
                'password' => Hash::make('password123'),
            ]
        );
        $owner->assignRole('owner');

        // Create default pegawai
        $pegawai = User::firstOrCreate(
            ['email' => 'pegawai@apotek.com'],
            [
                'name' => 'Pegawai Apotek',
                'password' => Hash::make('password123'),
            ]
        );
        $pegawai->assignRole('pegawai');

        $this->command->info('Default users seeded successfully!');
        $this->command->info('Owner: owner@apotek.com / password123');
        $this->command->info('Pegawai: pegawai@apotek.com / password123');
    }
}
