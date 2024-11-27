<?php

namespace App\Repositories;

use App\Contract\ProductVariantRepointerface;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Modules\Product\Models\ProductVariant;
use Modules\Product\Models\VariantOption;

class ProductVariantEloquentRepo extends EloquentRepository implements ProductVariantRepointerface
{

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
        $variantOption = VariantOption::findOrFail($data['variant_option_id']);

        if ($variantOption->type === 'color' && $data['value'] !== $variantOption->name) {
            throw new InvalidArgumentException(
                "Invalid color value. Expected: {$variantOption->name}"
            );
        }

        return $this->create([
            'product_id' => $productId,
            'variant_option_id' => $data['variant_option_id'],
            'value' => $data['value'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    public function updateProductVariant(Model $model, array $data)
    {
        $variantOption = $model->variantOption->findOrFail($data['variant_option_id']);

        if ($variantOption->type === 'color' && $data['value'] !== $variantOption->name) {
            throw new InvalidArgumentException(
                "Invalid color value. Expected: {$variantOption->name}"
            );
        }

        return $this->update($model, $data);
    }

    public function find(Model $model)
    {
        return $model->load('product');
    }
}
