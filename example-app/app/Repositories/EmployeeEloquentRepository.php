<?php

namespace App\Repositories;

use App\Contract\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;

class EmployeeEloquentRepository extends EloquentRepository implements EmployeeRepositoryInterface
{
    /**
     * get model
     * @return string
     */
    /**
     * getModel
     *
     * @return void
     */
    public function getModel()
    {
        return Employee::class;
    }

    /**
     * builderQuery
     *
     * @return void
     */
    public function builderQuery()
    {
        return $this->_model::query();
    }

    /**
     * find
     *
     * @param  mixed $model
     * @return void
     */
    public function find(Model $model)
    {
        return $model->load('permissions');
    }
}
