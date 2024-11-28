<?php

namespace App\Services;

use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariant;

class OrderService
{
    /**
     * cancelOrder
     *
     * @param  mixed $order
     * @param  mixed $oldOrderItems
     * @return void
     */
    public function cancelOrder($order, $oldOrderItems)
    {
        $oldOrderItems->each(function ($item) {
            if (isset($item->product_variant_id)) {
                $item->product_variant->increment('quantity', $item->quantity);
            } else {
                $item->product->increment('quantity', $item->quantity);
            }
        });
    }

    /**
     * updateOrderItems
     *
     * @param  mixed $orderItems
     * @param  mixed $oldItemsByProductId
     * @param  mixed $order
     * @return void
     */
    public function updateOrderItems($orderItems, $oldItemsByProductId, $order)
    {
        $orderItems->each(function ($item) use ($oldItemsByProductId, $order) {
            $oldItem = $oldItemsByProductId->get($item['product_id']);
            $quantityChange = $item['quantity'] - ($oldItem->quantity ?? 0);

            if (isset($item['product_variant_id'])) {
                ProductVariant::where('id', $item['product_variant_id'])
                    ->decrement('quantity', $quantityChange);
            } else {
                Product::where('id', $item['product_id'])
                    ->decrement('quantity', $quantityChange);
            }

            if ($oldItem) {
                $oldItem->update([
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            } else {
                $order->orderItem()->create($item);
            }
        });
    }

    /**
     * removeDeletedItems
     *
     * @param  mixed $oldItems
     * @param  mixed $orderItems
     * @return void
     */
    public function removeDeletedItems($oldItems, $orderItems)
    {
        $oldItems->whereNotIn('product_id', $orderItems->pluck('product_id'))
            ->each(function ($oldItem) {
                if (isset($oldItem->product_variant_id)) {
                    $oldItem->product_variant->increment('quantity', $oldItem->quantity);
                } else {
                    $oldItem->product->increment('quantity', $oldItem->quantity);
                }

                $oldItem->delete();
            });
    }
}
