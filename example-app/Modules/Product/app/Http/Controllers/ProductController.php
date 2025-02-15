<?php

namespace Modules\Product\Http\Controllers;

use App\Contract\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Models\Product;

class ProductController extends Controller
{
    /**
     * __construct
     *
     * @param  mixed $productRepository
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
        $perPage = $request->input('perPage', 60);
        $this->authorize('view', Product::class);
        $query = $this->productRepository
            ->builderQuery()
            ->searchByNameCode($request->search)
            ->searchByCategory($request->category_id);

        return $this->sendSuccess($query->paginate($perPage));
    }

    /**
     * store
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        // $this->authorize('create', Product::class);
        $validateData = $request->validated();
        $product = $this->productRepository->create($validateData);

        return $this->created($product);
    }

    /**
     * show
     *
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        // $this->authorize('view', Product::class);
        $product = $this->productRepository->find($product);

        return $this->sendSuccess($product);
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
        // $this->authorize('update', $product);
        $validateData = $request->validated();
        $product = $this->productRepository->update($product, $validateData);

        return $this->updated($product);
    }

    /**
     * destroy
     *
     * @param  mixed $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        // $this->authorize('delete', $product);
        $product->delete($product);

        return $this->deteled();
    }
}
