<?php

namespace App\Repositories;

use App\Helpers\OrderHelper;
use App\Models\Order;
use App\Repositories\EloquentRepository;
use Illuminate\Database\Eloquent\Model;

class OrderEloquentRepository extends EloquentRepository implements OrderRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Order::class;
    }

    public function select($search, $status, $created_by)
    {
        $query = $this->_model::query()->with('orderItem.product');

        return $query->when($search, function ($query, $search) {
            return $query->searchNameCodePhone($search);
        })
            ->when($status, function ($query, $status) {
                return $query->status($status);
            })
            ->when($created_by, function ($query, $created_by) {
                return $query->createdBy($created_by);
            })
            ->paginate(10);
    }

    public function createOrder(array $orderData, array $orderItems)
    {

        $order = $this->_model::create($orderData);
        $order->products()->attach($orderItems);

        foreach ($orderItems as $item) {
            $order->products()->where('products.id', $item['product_id'])
                ->decrement('products.quantity', $item['quantity']);
        }

        return $order;
    }

    public function updateOrder(Model $model, array $data)
    {
        $model->update(['status' => $data['status']]);
        $oldItems = $model->orderItem()->with('product')->get();
        $oldItemsByProductId = $oldItems->keyBy('product_id');
        $orderItems = collect($data['order_items']);

        if ($data['status'] === 'canceled') {
            OrderHelper::cancelOrder($model, $oldItems);
        } else {
            $model->update($data);
            OrderHelper::updateOrderItems($orderItems, $oldItemsByProductId, $model);
            OrderHelper::removeDeletedItems($oldItems, $orderItems);
        }
    }

    public function find(Model $model)
    {
        return $model->load('orderItem.product');
    }
}
