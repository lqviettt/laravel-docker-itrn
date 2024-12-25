<?php

namespace Modules\Product\Http\Controllers;

use App\Contract\ProductVariantRepointerface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
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

        return $this->sendSuccess($query->paginate($perPage));
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
            return $this->created($variant);
        } catch (\InvalidArgumentException $e) {
            return $this->sendError($e->getMessage());
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

        return $this->created($pvariant);
    }

    /**
     * update
     *
     * @param  ProductVariantRequest $request
     * @param  ProductVariant $pvariantId
     * @return JsonResponse
     */
    public function update(ProductVariantRequest $request, $pvariantId): JsonResponse
    {
        $validatedData = $request->validated();
        try {
            $pvariantId = $this->productVariantRepointerface->updateProductVariant($pvariantId, $validatedData);
            return $this->updated($pvariantId);
        } catch (\InvalidArgumentException $e) {
            return $this->sendError($e->getMessage());
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
        $pvariantId->delete($pvariantId);

        return $this->deteled();
    }
}
