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
            ['username' => 'admin'],
            [
                'name' => 'Admin Apotek',
                'password' => Hash::make('AdminApotik123'),
            ]
        );
        $owner->assignRole('owner');

        // Create default pegawai
        $pegawai = User::firstOrCreate(
            ['username' => 'ArRumFarma'],
            [
                'name' => 'ArRumFarma',
                'password' => Hash::make('ApotikArRum123'),
            ]
        );
        $pegawai->assignRole('pegawai');

        $this->command->info('Default users seeded successfully!');
    }
}
