<?php

namespace App\Repositories;

use App\Contracts\ProductIncomingRepositoryInterface;
use App\Models\ProductIncoming;

class ProductIncomingRepository extends BaseRepository implements ProductIncomingRepositoryInterface
{
    public function __construct(ProductIncoming $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters($query, array $filters): void
    {
        parent::applyFilters($query, $filters);

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

        // Add vendor filter
        if (!empty($filters['vendor_name'])) {
            $query->where('vendor_name', 'LIKE', "%{$filters['vendor_name']}%");
        }
    }
}
