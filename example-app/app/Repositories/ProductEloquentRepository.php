<?php

namespace App\Repositories;

use App\Contract\ProductRepositoryInterface;
use App\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Models\Product;

class ProductEloquentRepository extends EloquentRepository implements ProductRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Product::class;
    }

    public function builderQuery()
    {
        return $this->_model::query();
    }

    public function find(Model $model)
    {
        return $model->load('category', 'variants');
    }
}
