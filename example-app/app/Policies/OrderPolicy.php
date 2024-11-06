<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Services\PermissionService;

class OrderPolicy
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function view(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'view', 'order');
    }

    public function create(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'create', 'order');
    }

    public function update(User $user, Order $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'update', 'order');
    }

    public function delete(User $user, Order $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'delete', 'order');
    }
}
