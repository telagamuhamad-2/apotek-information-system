<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'owner@apotek.com')->first();
        $productTypes = ProductType::all();

        if ($productTypes->isEmpty()) {
            $this->command->warn('Product types not found. Please run ProductTypeSeeder first.');
            return;
        }

        $medicines = [
            'Paracetamol',
            'Amoxicillin',
            'Ibuprofen',
            'Omeprazole',
            'Cetirizine',
            'Loratadine',
            'Metformin',
            'Simvastatin',
            'Amlodipine',
            'Captopril',
            'Ambroxol',
            'Dextromethorphan',
            'Guaifenesin',
            'Vitamin C',
            'Vitamin D3',
            'Vitamin B Complex',
            'Calcium',
            'Iron',
            'Folic Acid',
            'Omega 3',
            'Probiotic',
            'Glucosamine',
            'Melatonin',
            'Collagen',
            'Coenzyme Q10',
        ];

        $brands = ['Bio', 'Farma', 'Med', 'Health', 'Care', 'Plus', 'Max', 'Pro', 'Ultra', 'Extra'];
        $purposes = ['Demam', 'Sakit Kepala', 'Batuk', 'Flu', 'Nyeri', 'Allergi', 'Diabetes', 'Tekanan Darah', 'Asam Urat', 'Vitalitas'];
        $vendors = [
            'PT. Kalbe Farma Tbk',
            'PT. Tempo Scan Pacific Tbk',
            'PT. Bayer Indonesia',
            'PT. Pfizer Indonesia',
            'PT. Novartis Indonesia',
            'PT. Sanofi Indonesia',
            'PT. AstraZeneca Indonesia',
            'PT. Boehringer Ingelheim Indonesia',
            'PT. Merck Tbk',
            'PT. Danone Indonesia',
            'PT. Kalbe Nutritionals',
            'PT. Sido Muncul',
            'PT. Bintang Toedjoe',
            'PT. Konimex',
            'PT. Soho Industri Pharmasi',
        ];

        $products = [];

        for ($i = 1; $i <= 100; $i++) {
            $productType = $productTypes->random();
            $prefix = $productType->product_type_prefix;
            $medicine = $medicines[array_rand($medicines)];
            $brand = $brands[array_rand($brands)];
            $purpose = $purposes[array_rand($purposes)];
            $vendor = $vendors[array_rand($vendors)];

            $productName = "{$brand} {$medicine} {$productType->product_type_name} {$i}";

            $products[] = [
                'product_code' => $prefix . str_pad($i, 5, '0', STR_PAD_LEFT),
                'product_name' => $productName,
                'product_type_id' => $productType->id,
                'product_purpose' => $purpose,
                'product_quantity' => rand(10, 500),
                'product_price' => rand(5000, 200000),
                'selling_price' => rand(7000, 250000),
                'purchase_price' => rand(4000, 150000),
                'product_expiration_date' => now()->addDays(rand(30, 1095))->format('Y-m-d'),
                'vendor_name' => $vendor,
                'last_updated_by' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Product::insert($products);

        $this->command->info('100 products seeded successfully!');
    }
}
