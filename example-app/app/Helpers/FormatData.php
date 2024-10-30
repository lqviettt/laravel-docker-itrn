<?php

namespace App\Helpers;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class FormatData
{
    /**
     * formatData
     *
     * @param  Order $order
     * @return array
     */
    public function formatData(LengthAwarePaginator|Collection  $orders): SupportCollection
    {
        if ($orders instanceof LengthAwarePaginator) {
            $orders = $orders->getCollection();
        }

        return $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'code' => $order->code,
                'created_by' => $order->created_by,
                'customer_name' => $order->lastname . ' ' . $order->firstname,
                'customer_phone' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'status' => $order->status,
                'shipping_address' => $order->shipping_address,
                'order_item' => $order->orderItem->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'order_id' => $item->order_id,
                        'product_id' => $item->product->id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
            ];
        });
    }
}
