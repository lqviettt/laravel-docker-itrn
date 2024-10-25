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

    public function select($search, $status)
    {
        $query = $this->_model::query();

        return $query->when($search, function ($query) use ($search) {
            return $query->searchName($search);
        })
            ->when($status, function ($query) use ($status) {
                return $query->status($status);
            })
            ->paginate(10);
    }

    public function find(Model $model)
    {
        return $model->load('product');
    }
}
