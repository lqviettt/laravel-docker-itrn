<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;

interface CategoryRepositoryInterface
{
    public function builderQuery();

    public function find(Model $model);
}
