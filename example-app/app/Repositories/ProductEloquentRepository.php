<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\Model;

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

    public function select($search, $status, $category_id)
    {
        $query = $this->_model::query()->with('orderItem.product');

        return $query->when($search, function ($query, $search) {
            return $query->searchNameCode($search);
        })
            ->when($status, function ($query, $status) {
                return $query->status($status);
            })
            ->when($category_id, function ($query, $category_id) {
                return $query->categoryId($category_id);
            })
            ->paginate(10);
    }

    public function getProductByCategory()
    {
        return $this->_model::where('category_id', '=', '27')->get();
    }

    public function find(Model $model)
    {
        return $model->load('category');
    }
}
