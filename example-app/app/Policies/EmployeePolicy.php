<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function view(User $user)
    {
        return $user->is_admin;
    }

    public function create(User $user)
    {
        return $user->is_admin;
    }

    public function update(User $user, Employee $employee)
    {
        return $user->is_admin;
    }

    public function delete(User $user, Employee $employee)
    {
        return $user->is_admin;
    }
}
