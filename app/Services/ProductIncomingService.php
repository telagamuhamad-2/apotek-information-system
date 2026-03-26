<?php

namespace App\Services;

use App\Contracts\ProductIncomingRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductIncomingService extends BaseService
{
    public function __construct(ProductIncomingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a new product incoming (purchase) and update/add product stock
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            // Add current user id as last_updated_by
            $data['last_updated_by'] = Auth::id();

            // Calculate total price
            $data['product_total_price'] = $data['product_quantity'] * $data['product_each_price'];

            // Create product incoming record
            $productIncoming = parent::create($data);

            // Find or create product by code
            if (!empty($data['product_code'])) {
                $product = Product::where('product_code', $data['product_code'])->first();

                if ($product) {
                    // Update existing product stock and purchase price
                    $product->product_quantity += $data['product_quantity'];

                    // Update purchase price (harga beli terakhir)
                    $product->purchase_price = $data['product_each_price'];

                    // Update other fields only if provided
                    if (!empty($data['product_expiration_date'])) {
                        $product->product_expiration_date = $data['product_expiration_date'];
                    }
                    if (!empty($data['product_name'])) {
                        $product->product_name = $data['product_name'];
                    }
                    if (!empty($data['product_type_id'])) {
                        $product->product_type_id = $data['product_type_id'];
                    }
                    if (!empty($data['product_purpose'])) {
                        $product->product_purpose = $data['product_purpose'];
                    }

                    // If selling price not set yet, set it to purchase price
                    if (!$product->selling_price) {
                        $product->selling_price = $data['product_each_price'];
                    }

                    $product->last_updated_by = Auth::id();
                    $product->save();
                } else {
                    // Create new product if not exists
                    Product::create([
                        'product_code' => $data['product_code'],
                        'product_name' => $data['product_name'] ?? 'Unknown',
                        'product_type_id' => $data['product_type_id'] ?? null,
                        'product_purpose' => $data['product_purpose'] ?? null,
                        'product_quantity' => $data['product_quantity'],
                        'product_price' => $data['product_each_price'],
                        'selling_price' => $data['product_each_price'], // Initially same as purchase price
                        'purchase_price' => $data['product_each_price'],
                        'product_expiration_date' => $data['product_expiration_date'] ?? null,
                        'last_updated_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            return $productIncoming;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to create product incoming: " . $e->getMessage());
        }
    }

    /**
     * Update product incoming and adjust stock difference
     */
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            // Get original incoming record
            $originalIncoming = $this->findById($id);

            if (!$originalIncoming) {
                throw new Exception("Record not found.");
            }

            // Calculate total price
            $data['product_total_price'] = $data['product_quantity'] * $data['product_each_price'];
            $data['last_updated_by'] = Auth::id();

            // Update incoming record
            $updatedIncoming = parent::update($id, $data);

            // Adjust product stock and purchase price
            if (!empty($data['product_code']) && !empty($originalIncoming->product_code)) {
                $product = Product::where('product_code', $data['product_code'])->first();

                if ($product) {
                    // Calculate stock difference
                    $oldQuantity = $originalIncoming->product_quantity;
                    $newQuantity = $data['product_quantity'];
                    $difference = $newQuantity - $oldQuantity;

                    // Adjust stock
                    $product->product_quantity += $difference;

                    // Update purchase price (harga beli terakhir)
                    $product->purchase_price = $data['product_each_price'];

                    // Update other fields only if provided
                    if (!empty($data['product_name'])) {
                        $product->product_name = $data['product_name'];
                    }
                    if (!empty($data['product_type_id'])) {
                        $product->product_type_id = $data['product_type_id'];
                    }
                    if (!empty($data['product_purpose'])) {
                        $product->product_purpose = $data['product_purpose'];
                    }
                    if (!empty($data['product_expiration_date'])) {
                        $product->product_expiration_date = $data['product_expiration_date'];
                    }

                    $product->last_updated_by = Auth::id();
                    $product->save();
                }
            }

            DB::commit();
            return $updatedIncoming;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to update product incoming: " . $e->getMessage());
        }
    }

    /**
     * Delete product incoming and revert stock change
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();

        try {
            $incoming = $this->findById($id);

            if ($incoming) {
                // Revert stock change
                $product = Product::where('product_code', $incoming->product_code)->first();

                if ($product) {
                    $product->product_quantity -= $incoming->product_quantity;

                    // Don't allow negative stock
                    if ($product->product_quantity < 0) {
                        $product->product_quantity = 0;
                    }

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
            throw new Exception("Failed to delete product incoming: " . $e->getMessage());
        }
    }
}
