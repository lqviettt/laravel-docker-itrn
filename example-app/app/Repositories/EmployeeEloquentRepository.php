<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class EmployeeEloquentRepository extends EloquentRepository implements EmployeeRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Employee::class;
    }

    public function builderQuery()
    {
        return $this->_model::query();
    }

    public function find(Model $model)
    {
        return $model->load('permissions');
    }
}
