<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Services\PermissionService;

class ProductPolicy
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function view(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'view', 'product');
    }

    public function create(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'create', 'product');
    }

    public function update(User $user, Product $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'update', 'product');
    }

    public function delete(User $user, Product $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'delete', 'product');
    }
}
