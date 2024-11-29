<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Models\Product;

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
        $perPage = $request->input('perPage', 5);
        $this->authorize('view', Product::class);
        $query = $this->productRepository
            ->builderQuery()
            ->searchByNameCode($request->search)
            ->SearchByCategory($request->category_id);

        return response()->json($query->paginate($perPage));
    }

    /**
     * store
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);
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
        $this->authorize('view', Product::class);
        $product = $this->productRepository->find($product);

        return response()->json($product);
    }

    /**
     * update
     *
     * @param  ProductRequest $request
     * @param  Product $product
     * @return JsonResponse
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);
        $validateData = $request->validated();
        $product = $this->productRepository->update($product, $validateData);

        return response()->json($product);
    }

    /**
     * destroy
     *
     * @param  mixed $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);
        $product->delete();

        return response()->json($product);
    }
}
