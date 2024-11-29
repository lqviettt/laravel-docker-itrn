<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PermissionService;
use Modules\Product\Models\Category;

class CategoryPolicy
{
    protected $permissionService;

    /**
     * __construct
     *
     * @param  PermissionService $permissionService
     * @return void
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * view
     *
     * @param  User $user
     * @return void
     */
    public function view(?User $user)
    {
        if (!$user) {
            return true;
        }

        return true;
    }

    /**
     * create
     *
     * @param  User $user
     * @return void
     */
    public function create(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'create', 'category');
    }

    /**
     * update
     *
     * @param  User $user
     * @param  Category $category
     * @return void
     */
    public function update(User $user, Category $category)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'update', 'category');
    }

    /**
     * delete
     *
     * @param  User $user
     * @param  Category $category
     * @return void
     */
    public function delete(User $user, Category $category)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'delete', 'category');
    }
}
