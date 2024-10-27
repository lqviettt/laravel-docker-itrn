<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface OrderRepositoryInterface
{
    public function builderQuery();

    public function createOrder(array $orderData, array $orderItems);

    public function updateOrder(Model $model, array $data);

    public function delete(Model $model);

    public function find(Model $model);
}
