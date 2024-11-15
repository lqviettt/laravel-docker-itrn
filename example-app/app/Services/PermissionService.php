<?php

namespace App\Services;

use App\Models\User;

class PermissionService
{
    public function hasPermission(?User $user, $action, $entity)
    {
        if (!$user->employee) {
            return false;
        }

        return $user->employee->permissions()
            ->where('name', $action)
            ->where('entity', $entity)
            ->exists();
    }
}
