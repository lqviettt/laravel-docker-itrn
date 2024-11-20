<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface VariantRepositoryInterface
{
    public function builderQuery();

    public function find(Model $model);
}
