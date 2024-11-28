<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PermissionService;
use Modules\Order\Models\Order;

class OrderPolicy
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
    public function view(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'view', 'order');
    }

    /**
     * create
     *
     * @param  mixed $user
     * @return void
     */
    public function create(User $user)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'create', 'order');
    }

    /**
     * update
     *
     * @param  mixed $user
     * @param  mixed $product
     * @return void
     */
    public function update(User $user, Order $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'update', 'order');
    }

    /**
     * delete
     *
     * @param  mixed $user
     * @param  mixed $product
     * @return void
     */
    public function delete(User $user, Order $product)
    {
        return $user->is_admin || $this->permissionService->hasPermission($user, 'delete', 'order');
    }
}
