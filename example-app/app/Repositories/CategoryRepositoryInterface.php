<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface CategoryRepositoryInterface
{
    public function builderQuery();

    public function find(Model $model);
}
