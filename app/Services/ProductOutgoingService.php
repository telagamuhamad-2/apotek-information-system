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
        DB::beginTransaction();

        try {
            // Find product by code
            $product = Product::where('product_code', $data['product_code'])->first();

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

            DB::commit();
            return $productOutgoing;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update product outgoing and adjust stock difference
     */
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            // Get original outgoing record
            $originalOutgoing = $this->findById($id);

            if (!$originalOutgoing) {
                throw new \Exception("Record not found.");
            }

            // Find product
            $product = Product::where('product_code', $data['product_code'])->first();

            if (!$product) {
                throw new \Exception("Obat dengan kode {$data['product_code']} tidak ditemukan!");
            }

            // Get selling price from product
            $sellingPrice = $product->selling_price ?? $product->product_price ?? 0;

            // Calculate stock adjustment
            $oldQuantity = $originalOutgoing->product_quantity;
            $newQuantity = $data['product_quantity'];
            $difference = $oldQuantity - $newQuantity; // Positive means adding back stock, negative means reducing more

            // Check if new quantity is valid
            if ($product->product_quantity + $difference < 0) {
                throw new \Exception("Stok tidak mencukupi! Stok tersedia setelah penyesuaian: " . ($product->product_quantity + $difference));
            }

            // Calculate total price with new selling price
            $data['product_total_price'] = $newQuantity * $data['product_each_price'];
            $data['last_updated_by'] = Auth::id();

            // Update outgoing record
            $updatedOutgoing = parent::update($id, $data);

            // Adjust product stock
            $product->product_quantity += $difference;
            $product->last_updated_by = Auth::id();
            $product->save();

            DB::commit();
            return $updatedOutgoing;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to update product outgoing: " . $e->getMessage());
        }
    }

    /**
     * Delete product outgoing and revert stock
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();

        try {
            $outgoing = $this->findById($id);

            if ($outgoing) {
                // Revert stock change
                $product = Product::where('product_code', $outgoing->product_code)->first();

                if ($product) {
                    $product->product_quantity += $outgoing->product_quantity;
                    $product->last_updated_by = Auth::id();
                    $product->save();
                }

                $result = parent::delete($id);
                DB::commit();
                return $result;
            }

            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to delete product outgoing: " . $e->getMessage());
        }
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
