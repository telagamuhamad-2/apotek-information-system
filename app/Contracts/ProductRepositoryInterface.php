<?php

namespace App\Contracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get the latest product code for a specific product type
     */
    public function getLatestCodeByType(int $productTypeId): ?string;
}
