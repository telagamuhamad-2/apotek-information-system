<?php

namespace App\Services;

use App\Contracts\ProductRepositoryInterface;
use App\Models\ProductType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService extends BaseService
{
    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a new product (master data only, stock starts at 0)
     * Stock will be managed through purchases (pembelian)
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Auto generate product code if not provided
            if (empty($data['product_code']) && !empty($data['product_type_id'])) {
                $data['product_code'] = $this->generateProductCode($data['product_type_id']);
            }

            // Set stock to 0 for new products
            // Stock will be added through pembelian (purchases)
            $data['product_quantity'] = 0;
            $data['last_updated_by'] = Auth::id();

            // If selling_price not set, default to 0 (must be set in Stock menu)
            if (!isset($data['selling_price'])) {
                $data['selling_price'] = 0;
            }

            return parent::create($data);
        });
    }

    /**
     * Generate product code based on type prefix and increment
     */
    public function generateProductCode(int $productTypeId): string
    {
        $productType = ProductType::lockForUpdate()->find($productTypeId);
        $prefix = $productType->product_type_prefix ?? 'OBT'; // Default to OBT if no prefix
        
        $latestCode = $this->repository->getLatestCodeByType($productTypeId);
        
        $nextNumber = 1;
        if ($latestCode) {
            // Extract number from code (e.g., OBT-0001 -> 1)
            $parts = explode('-', $latestCode);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $nextNumber = (int)$lastPart + 1;
            }
        }

        // Format: PREFIX-0001
        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update an existing product
     * Note: stock_quantity cannot be manually updated here
     */
    public function update(int $id, array $data)
    {
        // Remove product_quantity from data - stock is managed by purchases/sales
        unset($data['product_quantity']);
        $data['last_updated_by'] = Auth::id();

        // If selling_price not set, keep existing value
        if (!isset($data['selling_price'])) {
            unset($data['selling_price']);
        }

        return parent::update($id, $data);
    }
    public function updateSellingPrice(int $id, float $price)
    {
        $data = [
            'selling_price' => $price,
            'last_updated_by' => Auth::id(),
        ];
        return $this->update($id, $data);
    }
    public function updatePurchasePrice(int $id, float $price)
    {
        $data = [
            'purchase_price' => $price,
            'last_updated_by' => Auth::id(),
        ];
        return $this->update($id, $data);
    }
}
