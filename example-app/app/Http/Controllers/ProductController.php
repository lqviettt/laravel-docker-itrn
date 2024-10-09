<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Category;


class ProductController extends Controller
{
    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');

        $product = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like',  $search . '%')
                        ->orWhere('code', 'like', $search . '%')
                        ->orWhere('price', 'like', '%' . $search . '%');
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->select('id', 'name', 'code', 'category_id', 'description', 'price', 'status')
            ->get();

        return response()->json($product->makeHidden(['created_at', 'updated_at']));
    }

    /**
     * store
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validateData = $request->validate([
            "name" => ["required"],
            "price" => ["required"],
            "description" => ["required"],
            "code" => ["required"],
            "category_id" => ["required"],
            "status" => ["required"]
        ]);

        $product = Product::query()->create($validateData);

        return response()->json($product);
    }

    /**
     * show
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::query()->find($id);
        $category = Category::query()->find($product->category_id);
        $product->category = $category;

        return response()->json($product);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, int $id): JsonResponse // Form Request validate
    {
        $validateData = $request->validate([
            "name" => ["required"],
            "price" => ["required"],
            "description" => ["required"],
            "code" => ["required"],
            "category_id" => ["required"],
            "status" => ["required"]
        ]);

        $product = Product::query()->find($id)
            ->update($validateData);

        return response()->json($product);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::query()->find($id);
        $product->delete();

        return response()->json($product);
    }
}
