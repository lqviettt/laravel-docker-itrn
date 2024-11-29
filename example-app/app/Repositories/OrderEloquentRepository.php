<?php

namespace App\Repositories;

use App\Contract\OrderRepositoryInterface;
use App\Jobs\SendOrderEmailJob;
use App\Repositories\EloquentRepository;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\Order;

class OrderEloquentRepository extends EloquentRepository implements OrderRepositoryInterface
{
    protected $orderService;

    /**
     * __construct
     *
     * @param  OrderService $orderService
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return Order::class;
    }

    /**
     * builderQuery
     *
     * @return void
     */
    public function builderQuery()
    {
        return $this->_model::query()->with('orderItem.product');
    }

    /**
     * createOrder
     *
     * @param  mixed $orderData
     * @param  mixed $orderItems
     * @return void
     */
    public function createOrder(array $orderData, array $orderItems)
    {
        $order = $this->_model::create($orderData);
        $order->products()->attach($orderItems);

        foreach ($orderItems as $item) {
            if (isset($item['product_variant_id'])) {
                $order->product_variants()->where('product_variants.id', $item['product_variant_id'])
                    ->decrement('product_variants.quantity', $item['quantity']);
            } else {
                $order->products()->where('products.id', $item['product_id'])
                    ->decrement('products.quantity', $item['quantity']);
            }
        }

        $order->load('products', 'product_variants');
        SendOrderEmailJob::dispatch($order);

        return $order;
    }

    /**
     * updateOrder
     *
     * @param  mixed $model
     * @param  mixed $data
     * @return void
     */
    public function updateOrder(Model $model, array $data)
    {
        $model->update(['status' => $data['status']]);
        $oldItems = $model->orderItem()->with('product')->get();
        $oldItemsByProductId = $oldItems->keyBy('product_id');
        $orderItems = collect($data['order_items']);

        if ($data['status'] === 'canceled') {
            $this->orderService->cancelOrder($model, $oldItems);
        } else {
            $model->update($data);
            $this->orderService->updateOrderItems($orderItems, $oldItemsByProductId, $model);
            $this->orderService->removeDeletedItems($oldItems, $orderItems);
        }
    }

    /**
     * find
     *
     * @param  mixed $model
     * @return void
     */
    public function find(Model $model)
    {
        return $model->load('orderItem.product', 'orderItem.product_variant');
    }
}
