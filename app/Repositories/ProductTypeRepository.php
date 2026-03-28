<?php

namespace App\Repositories;

use App\Contracts\ProductTypeRepositoryInterface;
use App\Models\ProductType;

class ProductTypeRepository extends BaseRepository implements ProductTypeRepositoryInterface
{
    public function __construct(ProductType $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters($query, array $filters): void
    {
        parent::applyFilters($query, $filters);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('product_type_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_type_prefix', 'LIKE', "%{$search}%");
            });
        }
    }
}
