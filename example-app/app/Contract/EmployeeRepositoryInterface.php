<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Model;

interface EmployeeRepositoryInterface
{
    public function builderQuery();

    public function find(Model $model);

    public function create(array $data);

    public function update(Model $model, array $data);

    public function delete(Model $model);
}
