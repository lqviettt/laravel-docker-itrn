<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\OrderHistory;

class OrderHelper
{
    public static function cancelOrder($order, $oldOrderItems)
    {
        $oldOrderItems->each(function ($item) {
            $item->product->increment('quantity', $item->quantity);
        });
    }

    public static function updateOrderItems($orderItems, $oldItemsByProductId, $order)
    {
        $orderItems->each(function ($item) use ($oldItemsByProductId, $order) {
            $oldItem = $oldItemsByProductId->get($item['product_id']);
            $quantityChange = $item['quantity'] - ($oldItem->quantity ?? 0);

            Product::where('id', $item['product_id'])
                ->decrement('quantity', $quantityChange);

            if ($oldItem) {
                $oldItem->update([
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            } else {
                $order->orderItem()->create($item);
            }
        });
    }

    public static function removeDeletedItems($oldItems, $orderItems)
    {
        $oldItems->whereNotIn('product_id', $orderItems->pluck('product_id'))
            ->each(function ($oldItem) {
                $oldItem->product->increment('quantity', $oldItem->quantity);
                $oldItem->delete();
            });
    }
}
