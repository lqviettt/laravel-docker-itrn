<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Repositories\ProductVariantRepointerface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Models\ProductVariant;

class ProductVariantController extends Controller
{
    /**
     * __construct
     *
     * @param  mixed $productVariantRepointerface
     * @return void
     */
    public function __construct(protected ProductVariantRepointerface $productVariantRepointerface) {}
    
    /**
     * index
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $query = $this->productVariantRepointerface->builderQuery();

        return response()->json($query->paginate($perPage));
    }
    
    /**
     * store
     *
     * @param  ProductVariantRequest $request
     * @param  mixed $productId
     * @return JsonResponse
     */
    public function store(ProductVariantRequest $request, $productId): JsonResponse
    {
        $validatedData = $request->validated();
        try {
            $variant = $this->productVariantRepointerface->createProductVariant($validatedData, $productId);
            return response()->json($variant, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * show
     *
     * @param  ProductVariant $pvariantId
     * @return JsonResponse
     */
    public function show(ProductVariant $pvariantId): JsonResponse
    {
        $pvariant = $this->productVariantRepointerface->find($pvariantId);

        return response()->json($pvariant);
    }
    
    /**
     * update
     *
     * @param  ProductVariantRequest $request
     * @param  ProductVariant $pvariantId
     * @return JsonResponse
     */
    public function update(ProductVariantRequest $request, ProductVariant $pvariantId): JsonResponse
    {
        $validatedData = $request->validated();
        try {
            $pvariantId = $this->productVariantRepointerface->updateProductVariant($pvariantId, $validatedData);
            return response()->json($pvariantId, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * delete
     *
     * @param  ProductVariant $pvariantId
     * @return JsonResponse
     */
    public function delete(ProductVariant $pvariantId): JsonResponse
    {
        $pvariantId = $this->productVariantRepointerface->delete($pvariantId);

        return response()->json($pvariantId);
    }
}
