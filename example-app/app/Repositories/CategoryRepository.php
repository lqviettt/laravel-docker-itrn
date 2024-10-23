<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all($search, $status)
    {
        $query = Category::query()->select('id', 'name', 'status');

        if ($search) {
            $query->searchName($search);
        }

        if ($status) {
            $query->status($status);
        }

        return $query->paginate(20);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function find(Category $category)
    {
        $category->get();

        return $category;
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category)
    {
        $category->delete();

        return $category;
    }
}
