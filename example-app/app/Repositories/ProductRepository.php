<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function all($search, $status, $category_id)
    {
        $query = Product::query()->with('category:id,name')
            ->select('id', 'name', 'code', 'quantity', 'category_id', 'description', 'price', 'status',);

        if ($search) {
            $query->searchNameCode($search);
        }

        if ($status) {
            $query->status($status);//bug khi filter status=0 :((
        }

        if ($category_id) {
            $query->categoryId($category_id);
        }

        return $query->paginate(10);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function find(Product $product)
    {
        $product->get();

        return $product;
    }

    public function update(Product $product, array $data)
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product)
    {
        $product->delete();

        return $product;
    }
}
