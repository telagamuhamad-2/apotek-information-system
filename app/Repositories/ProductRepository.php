<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Get the latest product code for a specific product type
     */
    public function getLatestCodeByType(int $productTypeId): ?string
    {
        return $this->model
            ->where('product_type_id', $productTypeId)
            ->whereNotNull('product_code')
            ->orderBy('product_code', 'desc')
            ->lockForUpdate() // Handle race condition by locking the latest product row for this type
            ->value('product_code');
    }

    protected function applyFilters($query, array $filters): void
    {
        parent::applyFilters($query, $filters);

        // Global search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        // Add relationship filter for product_type_id
        if (!empty($filters['product_type_id'])) {
            $query->where('product_type_id', $filters['product_type_id']);
        }

        // Add expiration date filter
        if (!empty($filters['expiration_from'])) {
            $query->where('product_expiration_date', '>=', $filters['expiration_from']);
        }
        if (!empty($filters['expiration_to'])) {
            $query->where('product_expiration_date', '<=', $filters['expiration_to']);
        }

        // Add quantity filter
        if (isset($filters['min_quantity'])) {
            $query->where('product_quantity', '>=', $filters['min_quantity']);
        }
        if (isset($filters['max_quantity'])) {
            $query->where('product_quantity', '<=', $filters['max_quantity']);
        }

        // Add price filter
        if (isset($filters['min_price'])) {
            $query->where('product_price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('product_price', '<=', $filters['max_price']);
        }

        // Low stock filter (quantity < 10)
        if (isset($filters['low_stock']) && $filters['low_stock'] === '1') {
            $query->where('product_quantity', '<', 10);
        }

        // Expired filter
        if (isset($filters['expired']) && $filters['expired'] === '1') {
            $query->where('product_expiration_date', '<', now()->format('Y-m-d'));
        }
    }
}
