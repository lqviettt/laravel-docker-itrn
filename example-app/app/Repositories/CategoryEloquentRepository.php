<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\Model;

class CategoryEloquentRepository extends EloquentRepository implements CategoryRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Category::class;
    }

    public function builderQuery()
    {
        return $this->_model::query();
    }

    public function find(Model $model)
    {
        return $model->load('product');
    }
}
