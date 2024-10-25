<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface
{

    public function select($search, $status, $category_id);

    public function getProductByCategory();

    public function find(Model $model);
}
