<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;

interface VariantRepositoryInterface
{
    public function builderQuery();

    public function find(Model $model);
}
