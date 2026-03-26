<?php

namespace App\Services;

use App\Contracts\ProductTypeRepositoryInterface;

class ProductTypeService extends BaseService
{
    public function __construct(ProductTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
