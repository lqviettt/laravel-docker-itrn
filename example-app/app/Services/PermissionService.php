<?php

namespace App\Services;

use App\Models\User;

class PermissionService
{
    public function hasPermission(User $user, $action, $entity)
    {
        return $user->employee->permissions()
            ->where('name', $action)
            ->where('entity', $entity)
            ->exists();
    }
}
