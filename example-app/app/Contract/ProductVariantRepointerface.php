<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;

interface ProductVariantRepointerface
{
    public function builderQuery();

    public function createProductVariant(array $data, $productId);

    public function updateProductVariant($id, array $data);

    public function find(Model $model);
}
