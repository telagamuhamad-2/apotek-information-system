<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $pegawai = Role::firstOrCreate(['name' => 'pegawai']);

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Product Types
            'view product types',
            'create product types',
            'edit product types',
            'delete product types',

            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Purchases (Pembelian)
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',

            // Sales (Penjualan)
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to owner
        $owner->givePermissionTo(Permission::all());

        // Assign only sales permissions to pegawai
        $pegawai->givePermissionTo([
            'view sales',
            'create sales',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
