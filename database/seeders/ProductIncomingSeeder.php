<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductIncoming;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductIncomingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'owner@apotek.com')->first();
        $products = Product::take(100)->get();

        if ($products->isEmpty()) {
            $this->command->warn('Products not found. Please run ProductSeeder first.');
            return;
        }

        $incomings = [];

        foreach ($products as $index => $product) {
            $productType = ProductType::find($product->product_type_id);
            $quantity = rand(10, 200);
            $eachPrice = $product->purchase_price ?: ($product->product_price * 0.8);
            $totalPrice = $quantity * $eachPrice;

            $incomings[] = [
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'product_type_id' => $product->product_type_id,
                'product_purpose' => $product->product_purpose,
                'product_quantity' => $quantity,
                'product_each_price' => $eachPrice,
                'product_total_price' => $totalPrice,
                'vendor_name' => $product->vendor_name,
                'last_updated_by' => $user?->id,
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now(),
            ];
        }

        ProductIncoming::insert($incomings);

        $this->command->info('100 product incomings seeded successfully!');
    }
}
