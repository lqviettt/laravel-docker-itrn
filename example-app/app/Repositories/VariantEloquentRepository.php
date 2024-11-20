<?php

namespace App\Repositories;

use App\Contract\VariantRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Models\VariantOption;

class VariantEloquentRepository extends EloquentRepository implements VariantRepositoryInterface
{
    public function getModel()
    {
        return VariantOption::class;
    }

    public function builderQuery()
    {
        return $this->_model::query();
    }

    public function find(Model $model)
    {
        return $model->load('productVariants');
    }
}
