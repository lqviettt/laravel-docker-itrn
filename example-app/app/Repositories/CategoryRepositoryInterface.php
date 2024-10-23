<?php

namespace App\Repositories;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function all($search, $status);

    public function create(array $data);

    public function find(Category $category);

    public function update(Category $category, array $data);
    
    public function delete(Category $category);
}
