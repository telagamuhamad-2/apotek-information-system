<?php

namespace App\Services;

use App\Contracts\ProductIncomingRepositoryInterface;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductIncomingService extends BaseService
{
    protected ProductService $productService;

    public function __construct(ProductIncomingRepositoryInterface $repository, ProductService $productService)
    {
        parent::__construct($repository);
        $this->productService = $productService;
    }

    /**
     * Create a new product incoming (purchase) and update/add product stock
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Add current user id as last_updated_by
            $data['last_updated_by'] = Auth::id();

            // Calculate total price
            $data['product_total_price'] = $data['product_quantity'] * $data['product_each_price'];

            // Find or create product first
            $product = null;
            if (!empty($data['product_code'])) {
                $product = Product::where('product_code', $data['product_code'])->lockForUpdate()->first();
            }

            if ($product) {
                // Update existing product stock and purchase price
                $product->product_quantity += $data['product_quantity'];
                $product->purchase_price = $data['product_each_price'];

                // Update metadata if provided
                if (!empty($data['product_name'])) $product->product_name = $data['product_name'];
                if (!empty($data['product_type_id'])) $product->product_type_id = $data['product_type_id'];
                if (!empty($data['product_purpose'])) $product->product_purpose = $data['product_purpose'];
                if (!empty($data['product_expiration_date'])) $product->product_expiration_date = $data['product_expiration_date'];
                if (!empty($data['vendor_name'])) $product->vendor_name = $data['vendor_name'];

                $product->last_updated_by = Auth::id();
                $product->save();
            } else {
                // Create new product using ProductService to handle auto-code generation
                $productData = [
                    'product_code' => $data['product_code'] ?? null,
                    'product_name' => $data['product_name'],
                    'product_type_id' => $data['product_type_id'] ?? null,
                    'product_purpose' => $data['product_purpose'] ?? null,
                    'purchase_price' => $data['product_each_price'],
                    'product_expiration_date' => $data['product_expiration_date'] ?? null,
                    'vendor_name' => $data['vendor_name']
                ];

                $product = $this->productService->create($productData);
                
                // Now manually update quantity since ProductService::create sets it to 0
                $product->product_quantity = $data['product_quantity'];
                $product->save();
                
                // Ensure the incoming record has the correct code if it was auto-generated
                $data['product_code'] = $product->product_code;
            }

            // Create product incoming record
            return parent::create($data);
        });
    }

    /**
     * Update product incoming and adjust stock difference
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Get original incoming record
            $originalIncoming = $this->findById($id);

            if (!$originalIncoming) {
                throw new Exception("Record not found.");
            }

            // Calculate total price
            $data['product_total_price'] = $data['product_quantity'] * $data['product_each_price'];
            $data['last_updated_by'] = Auth::id();

            // Find product
            $product = Product::where('product_code', $originalIncoming->product_code)->lockForUpdate()->first();

            if ($product) {
                // Calculate stock difference
                $oldQuantity = $originalIncoming->product_quantity;
                $newQuantity = $data['product_quantity'];
                $difference = $newQuantity - $oldQuantity;

                // Adjust stock
                $product->product_quantity += $difference;
                $product->purchase_price = $data['product_each_price'];

                // Update metadata
                if (!empty($data['product_name'])) $product->product_name = $data['product_name'];
                if (!empty($data['product_type_id'])) $product->product_type_id = $data['product_type_id'];
                if (!empty($data['product_purpose'])) $product->product_purpose = $data['product_purpose'];
                if (!empty($data['product_expiration_date'])) $product->product_expiration_date = $data['product_expiration_date'];
                if (!empty($data['vendor_name'])) $product->vendor_name = $data['vendor_name'];

                $product->last_updated_by = Auth::id();
                $product->save();
            }

            // Update incoming record
            return parent::update($id, $data);
        });
    }

    /**
     * Delete product incoming and revert stock change
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $incoming = $this->findById($id);

            if ($incoming) {
                // Revert stock change
                $product = Product::where('product_code', $incoming->product_code)->lockForUpdate()->first();

                if ($product) {
                    $product->product_quantity -= $incoming->product_quantity;
                    if ($product->product_quantity < 0) $product->product_quantity = 0;

                    $product->last_updated_by = Auth::id();
                    $product->save();
                }

                return parent::delete($id);
            }

            return false;
        });
    }
}
