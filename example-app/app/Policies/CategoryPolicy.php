<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Services\PermissionService;

class CategoryPolicy
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function view(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'view', 'category');
    }

    public function create(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'create', 'category');
    }

    public function update(User $user, Category $category)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'update', 'category');
    }

    public function delete(User $user, Category $category)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'delete', 'category');
    }
}
