<?php

namespace App\Repositories;

use App\Helpers\OrderHelper;
use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function all($search, $status, $created_by)
    {
        $query = Order::query()->with('orderItem.product');

        if ($search) {
            $query->searchNameCodePhone($search);
        }

        if ($status) {
            $query->status($status);//bug khi filter status=0 còn vơis = 1 thi ok :((
        }

        if ($created_by) {
            $query->createdBy($created_by);
        }

        return $query->paginate(5);
    }

    public function create(array $orderData, array $orderItems)
    {

        $order = Order::create($orderData);
        $order->products()->attach($orderItems);

        foreach ($orderItems as $item) {
            $order->products()->where('products.id', $item['product_id'])
                ->decrement('products.quantity', $item['quantity']);
        }

        return $order;
    }

    public function find(Order $order)
    {
        return $order->load('orderItem.product');
    }

    public function update(Order $order, array $data)
    {
        $order->update(['status' => $data['status']]);
        $oldItems = $order->orderItem()->with('product')->get();
        $oldItemsByProductId = $oldItems->keyBy('product_id');
        $orderItems = collect($data['order_items']);

        if ($data['status'] === 'canceled') {
            OrderHelper::cancelOrder($order, $oldItems);
        } else {
            $order->update($data);
            OrderHelper::updateOrderItems($orderItems, $oldItemsByProductId, $order);
            OrderHelper::removeDeletedItems($oldItems, $orderItems);
        }
    }


    public function delete(Order $order)
    {
        return $order->delete();
    }
}
