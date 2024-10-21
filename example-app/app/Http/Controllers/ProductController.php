<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Traits\SearchTrait;

class ProductController extends Controller
{
    use SearchTrait;

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

        $products = $this->applySearch(Product::query(), $search, $status, $categoryId, null, 'product')
            ->with('category:id,name')
            ->select('id', 'name', 'code', 'quantity', 'category_id', 'description', 'price', 'status',)
            ->paginate(10);

        return response()->json($products->makeHidden(['created_at', 'updated_at']));
    }

    /**
     * store
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $validateData = $request->validated();
        $product = Product::query()->create($validateData);

        return response()->json($product);
    }

    /**
     * show
     *
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return response()->json($product);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $validateData = $request->validated();
        $product->update($validateData);

        return response()->json($product);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json($product);
    }
}
