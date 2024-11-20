<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\VariantRequest;
use App\Repositories\VariantRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Models\VariantOption;

class VariantOptionController extends Controller
{    
    /**
     * __construct
     *
     * @param  mixed $variantRepository
     * @return void
     */
    public function __construct(protected VariantRepositoryInterface $variantRepository) {}
    
    /**
     * index
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $query = $this->variantRepository
        ->builderQuery()
        ->searchByType($request->type);

        return response()->json($query->paginate($perPage));
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(VariantRequest $request): JsonResponse
    {
        $validateData = $request->validated();
        $variant = $this->variantRepository->create($validateData);

        return response()->json($variant);
    }
    
    /**
     * show
     *
     * @param  mixed $variant
     * @return JsonResponse
     */
    public function show(VariantOption $variant): JsonResponse
    {
        $variant = $this->variantRepository->find($variant);

        return response()->json($variant);
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $variant
     * @return JsonResponse
     */
    public function update(VariantRequest $request, VariantOption $variant): JsonResponse
    {
        $validateData = $request->validated();
        $variant = $this->variantRepository->update($variant, $validateData);

        return response()->json($variant);
    }
    
    /**
     * destroy
     *
     * @param  mixed $variant
     * @return JsonResponse
     */
    public function destroy(VariantOption $variant): JsonResponse
    {
        $variant = $this->variantRepository->delete($variant);

        return response()->json($variant);
    }
}
