<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function all($search, $status, $created_by);

    public function create(array $orderData, array $orderItems);

    public function find(Order $order);

    public function update(Order $order, array $data);
    
    public function delete(Order $order);
}
