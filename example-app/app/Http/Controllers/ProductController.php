<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepositoryInterface;

class ProductController extends Controller
{
    protected $productRepository;
    
    /**
     * __construct
     *
     * @param  mixed $productRepository
     * @return void
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        return $this->productRepository = $productRepository;
    }

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

        $products = $this->productRepository->all($search, $status, $categoryId);

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
        $product = $this->productRepository->delete($product);

        return response()->json($product);
    }
}
