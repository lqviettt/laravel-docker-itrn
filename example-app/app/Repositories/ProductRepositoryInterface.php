<?php

namespace App\Repositories;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface
{

    public function builderQuery();

    public function find(Model $model);

    public function create(array $data);

    public function update(Model $model, array $data);
}
