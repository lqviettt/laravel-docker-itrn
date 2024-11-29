<?php

namespace App\Policies;

use App\Models\User;
use Modules\Employee\Models\Employee;

class EmployeePolicy
{    
    /**
     * view
     *
     * @param  mixed $user
     * @return void
     */
    public function view(User $user)
    {
        return $user->is_admin;
    }
    
    /**
     * create
     *
     * @param  mixed $user
     * @return void
     */
    public function create(User $user)
    {
        return $user->is_admin;
    }
    
    /**
     * update
     *
     * @param  mixed $user
     * @param  mixed $employee
     * @return void
     */
    public function update(User $user, Employee $employee)
    {
        return $user->is_admin;
    }
    
    /**
     * delete
     *
     * @param  mixed $user
     * @param  mixed $employee
     * @return void
     */
    public function delete(User $user, Employee $employee)
    {
        return $user->is_admin;
    }
}
