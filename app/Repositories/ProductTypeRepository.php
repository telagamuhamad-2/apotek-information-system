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
}
