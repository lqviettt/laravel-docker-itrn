<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepositoryInterface;

class ProductController extends Controller
{
    /**
     * __construct
     *
     * @param  ProductRepositoryInterface $productRepository
     * @return void
     */
    public function __construct(protected ProductRepositoryInterface $productRepository) {}

    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $query = $this->productRepository
            ->builderQuery()
            ->searchByNameCode($request->search)
            ->SearchByCategory($request->categoryId);

        return response()->json($query->paginate(10)->makeHidden(['created_at', 'updated_at']));
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
        $product = $this->productRepository->create($validateData);

        return response()->json($product);
    }

    /**
     * show
     *
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        $product = $this->productRepository->find($product);

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
        $product = $this->productRepository->update($product, $validateData);

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
