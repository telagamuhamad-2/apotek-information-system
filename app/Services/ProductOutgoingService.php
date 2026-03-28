<?php

namespace App\Services;

use App\Contracts\ProductOutgoingRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductOutgoingService extends BaseService
{
    public function __construct(ProductOutgoingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a new product outgoing (sale) and decrease product stock
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Find product by code with lock
            $product = Product::where('product_code', $data['product_code'])->lockForUpdate()->first();

            if (!$product) {
                throw new \Exception("Obat dengan kode {$data['product_code']} tidak ditemukan!");
            }

            // Get selling price from product
            $sellingPrice = $product->selling_price ?? $product->product_price ?? 0;

            // Check if sufficient stock
            if ($product->product_quantity < $data['product_quantity']) {
                throw new \Exception("Stok tidak mencukupi! Stok tersedia: {$product->product_quantity}");
            }

            // Add current user id as last_updated_by
            $data['last_updated_by'] = Auth::id();

            // Set product details from product record
            $data['product_name'] = $product->product_name;
            $data['product_type_id'] = $product->product_type_id;
            $data['product_purpose'] = $product->product_purpose;
            $data['product_each_price'] = $sellingPrice; // Use selling price from product
            $data['product_total_price'] = $data['product_quantity'] * $sellingPrice;

            // Create product outgoing record
            $productOutgoing = parent::create($data);

            // Decrease product stock
            $product->product_quantity -= $data['product_quantity'];
            $product->last_updated_by = Auth::id();
            $product->save();

            return $productOutgoing;
        });
    }

    /**
     * Update product outgoing and adjust stock difference
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Get original outgoing record
            $originalOutgoing = $this->findById($id);

            if (!$originalOutgoing) {
                throw new \Exception("Record not found.");
            }

            // Find product
            $product = Product::where('product_code', $originalOutgoing->product_code)->lockForUpdate()->first();

            if (!$product) {
                throw new \Exception("Obat tidak ditemukan!");
            }

            // Calculate stock adjustment
            $oldQuantity = $originalOutgoing->product_quantity;
            $newQuantity = $data['product_quantity'];
            $difference = $oldQuantity - $newQuantity; // Positive means adding back stock, negative means reducing more

            // Check if new quantity is valid
            if ($product->product_quantity + $difference < 0) {
                throw new \Exception("Stok tidak mencukupi! Stok tersedia setelah penyesuaian: " . ($product->product_quantity + $difference));
            }

            // Calculate total price with provided price or product price
            $eachPrice = $data['product_each_price'] ?? $product->selling_price;
            $data['product_total_price'] = $newQuantity * $eachPrice;
            $data['last_updated_by'] = Auth::id();

            // Update outgoing record
            $updatedOutgoing = parent::update($id, $data);

            // Adjust product stock
            $product->product_quantity += $difference;
            $product->last_updated_by = Auth::id();
            $product->save();

            return $updatedOutgoing;
        });
    }

    /**
     * Delete product outgoing and revert stock
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $outgoing = $this->findById($id);

            if ($outgoing) {
                // Revert stock change
                $product = Product::where('product_code', $outgoing->product_code)->lockForUpdate()->first();

                if ($product) {
                    $product->product_quantity += $outgoing->product_quantity;
                    $product->last_updated_by = Auth::id();
                    $product->save();
                }

                return parent::delete($id);
            }

            return false;
        });
    }

    /**
     * Get products that are in stock for sales
     */
    public function getAvailableProducts()
    {
        return Product::where('product_quantity', '>', 0)
            ->orderBy('product_name')
            ->get();
    }

    /**
     * Get product by code
     */
    public function getProductByCode(string $code)
    {
        return Product::where('product_code', $code)->first();
    }

    /**
     * Check if product has sufficient stock
     */
    public function checkStock(string $code, int $quantity): bool
    {
        $product = Product::where('product_code', $code)->first();
        return $product && $product->product_quantity >= $quantity;
    }
}
