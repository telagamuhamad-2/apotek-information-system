<?php

namespace App\Services;

use App\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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
        // Set stock to 0 for new products
        // Stock will be added through pembelian (purchases)
        $data['product_quantity'] = 0;
        $data['last_updated_by'] = Auth::id();

        // If selling_price not set, use purchase_price as default
        if (!isset($data['selling_price'])) {
            $data['selling_price'] = $data['purchase_price'] ?? $data['product_price'] ?? 0;
        }

        return parent::create($data);
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

    /**
     * Update selling price only
     */
    public function updateSellingPrice(int $id, float $price)
    {
        $data = [
            'selling_price' => $price,
            'last_updated_by' => Auth::id(),
        ];
        return $this->update($id, $data);
    }

    /**
     * Update purchase price only
     */
    public function updatePurchasePrice(int $id, float $price)
    {
        $data = [
            'purchase_price' => $price,
            'last_updated_by' => Auth::id(),
        ];
        return $this->update($id, $data);
    }
}
