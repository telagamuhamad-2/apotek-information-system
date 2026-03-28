<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOutgoing;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductOutgoingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'pegawai@apotek.com')->first();
        $products = Product::take(100)->get();

        if ($products->isEmpty()) {
            $this->command->warn('Products not found. Please run ProductSeeder first.');
            return;
        }

        $outgoings = [];

        foreach ($products as $index => $product) {
            $productType = ProductType::find($product->product_type_id);
            $maxQuantity = min($product->product_quantity ?: 50, 20);
            $quantity = rand(1, max(1, $maxQuantity));
            $eachPrice = $product->selling_price ?: $product->product_price;
            $totalPrice = $quantity * $eachPrice;

            $outgoings[] = [
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'product_type_id' => $product->product_type_id,
                'product_purpose' => $product->product_purpose,
                'product_quantity' => $quantity,
                'product_each_price' => $eachPrice,
                'product_total_price' => $totalPrice,
                'last_updated_by' => $user?->id,
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now(),
            ];
        }

        ProductOutgoing::insert($outgoings);

        $this->command->info('100 product outgoings seeded successfully!');
    }
}
