<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $productTypes = [
            ['product_type_name' => 'Tablet', 'product_type_prefix' => 'TAB'],
            ['product_type_name' => 'Kapsul', 'product_type_prefix' => 'KAP'],
            ['product_type_name' => 'Sirup', 'product_type_prefix' => 'SYR'],
            ['product_type_name' => 'Salep', 'product_type_prefix' => 'SLP'],
            ['product_type_name' => 'Krim', 'product_type_prefix' => 'KRM'],
            ['product_type_name' => 'Tetes Mata', 'product_type_prefix' => 'TET'],
            ['product_type_name' => 'Tetes Hidung', 'product_type_prefix' => 'TTH'],
            ['product_type_name' => 'Inhaler', 'product_type_prefix' => 'INH'],
            ['product_type_name' => 'Suppositoria', 'product_type_prefix' => 'SUP'],
            ['product_type_name' => 'Serbuk', 'product_type_prefix' => 'SRB'],
            ['product_type_name' => 'Injeksi', 'product_type_prefix' => 'INJ'],
            ['product_type_name' => 'Pil', 'product_type_prefix' => 'PIL'],
        ];

        foreach ($productTypes as $type) {
            ProductType::firstOrCreate($type);
        }

        $this->command->info('Product types seeded successfully!');
    }
}
