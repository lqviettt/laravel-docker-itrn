<?php

namespace App\Repositories;

use App\Contract\VariantRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Models\VariantOption;

class VariantEloquentRepository extends EloquentRepository implements VariantRepositoryInterface
{
    /**
     * getModel
     *
     * @return void
     */
    public function getModel()
    {
        return VariantOption::class;
    }

    /**
     * builderQuery
     *
     * @return void
     */
    public function builderQuery()
    {
        return $this->_model::query();
    }

    /**
     * find
     *
     * @param  mixed $model
     * @return void
     */
    public function find(Model $model)
    {
        return $model->load('productVariants');
    }
}
