<?php

namespace App\Repositories;

use App\Contract\ProductVariantRepointerface;
use App\Services\VariantOptionService;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Models\ProductVariant;

class ProductVariantEloquentRepo extends EloquentRepository implements ProductVariantRepointerface
{
    protected $variantOptionService;

    /**
     * __construct
     *
     * @param  VariantOptionService $variantOptionService
     * @return void
     */
    public function __construct(VariantOptionService $variantOptionService)
    {
        parent::__construct();
        $this->variantOptionService = $variantOptionService;
    }

    public function getModel()
    {
        return ProductVariant::class;
    }

    public function builderQuery()
    {
        return $this->_model::query();
    }

    public function createProductVariant(array $data, $productId)
    {
        $variantOption = $this->variantOptionService->findOrFail($data['variant_option_id']);
        $this->variantOptionService->validate($data, $variantOption);

        return $this->create([
            'product_id' => $productId,
            'variant_option_id' => $data['variant_option_id'],
            'value' => $data['value'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    public function updateProductVariant($id, array $data)
    {
        $model = $this->builderQuery()->findOrFail($id);
        $variantOption = $this->variantOptionService->findOrFail($data['variant_option_id']);
        $this->variantOptionService->validate($data, $variantOption);

        return $this->update($model, $data);
    }

    public function find(Model $model)
    {
        return $model->load('product');
    }
}
