<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PermissionService;
use Modules\Product\Models\Product;

class ProductPolicy
{
    protected $permissionService;

    /**
     * __construct
     *
     * @param  mixed $permissionService
     * @return void
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * view
     *
     * @param  mixed $user
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
     * @param  mixed $user
     * @return void
     */
    public function create(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'create', 'product');
    }

    /**
     * update
     *
     * @param  mixed $user
     * @param  mixed $product
     * @return void
     */
    public function update(User $user, Product $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'update', 'product');
    }

    /**
     * delete
     *
     * @param  mixed $user
     * @param  mixed $product
     * @return void
     */
    public function delete(User $user, Product $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'delete', 'product');
    }
}
