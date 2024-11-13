<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;

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
