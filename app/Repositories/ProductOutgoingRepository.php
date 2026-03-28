<?php

namespace App\Repositories;

use App\Contracts\ProductOutgoingRepositoryInterface;
use App\Models\ProductOutgoing;

class ProductOutgoingRepository extends BaseRepository implements ProductOutgoingRepositoryInterface
{
    public function __construct(ProductOutgoing $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters($query, array $filters): void
    {
        parent::applyFilters($query, $filters);

        // Global search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        // Add relationship filter for product_type_id
        if (!empty($filters['product_type_id'])) {
            $query->where('product_type_id', $filters['product_type_id']);
        }

        // Add date filter
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        // Add price filter
        if (isset($filters['min_price'])) {
            $query->where('product_total_price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('product_total_price', '<=', $filters['max_price']);
        }

        // Add customer filter
        if (!empty($filters['customer_name'])) {
            $query->where('customer_name', 'LIKE', "%{$filters['customer_name']}%");
        }
    }
}
