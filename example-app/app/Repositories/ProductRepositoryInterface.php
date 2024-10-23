<?php

namespace App\Repositories;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function all($search, $status, $category_id);

    public function create(array $data);

    public function find(Product $product);

    public function update(Product $product, array $data);
    
    public function delete(Product $product);
}
