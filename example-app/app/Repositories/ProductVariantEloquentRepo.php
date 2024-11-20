<?php

namespace App\Repositories;

use App\Contract\ProductVariantRepointerface;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Modules\Product\Models\ProductVariant;

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
        dd($this->_model); // null ?

        $variantOption = $this->_model->variantOption->findOrFail($data['variant_option_id']);

        $allowedTypes = ['color', 'storage'];
        if (in_array($variantOption->type, $allowedTypes) && $data['value'] !== $variantOption->name) {
            throw new InvalidArgumentException(
                "Invalid {$variantOption->type} value. Expected: {$variantOption->name}"
            );
        }

        return $this->create([
            'product_id' => $productId,
            'variant_option_id' => $data['variant_option_id'],
            'value' => $data['value'],
            'price' => $data['price'],
        ]);
    }

    public function updateProductVariant(Model $model, array $data)
    {
        $variantOption = $model->variantOption->findOrFail($data['variant_option_id']);

        $allowedTypes = ['color', 'storage'];
        if (in_array($variantOption->type, $allowedTypes) && $data['value'] !== $variantOption->name) {
            throw new InvalidArgumentException(
                "Invalid {$variantOption->type} value. Expected: {$variantOption->name}"
            );
        }

        return $this->update($model, $data);
    }

    public function find(Model $model)
    {
        return $model->load('product');
    }
}
