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

        if ($search) {
            $product = Product::where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->select('id', 'name', 'code', 'description', 'price')
                ->get();
        } else {
            $product = Product::all()->makeHidden(['created_at', 'updated_at']);
        }

        return response()->json($product);
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