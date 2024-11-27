<?php

namespace Modules\Order\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class FormatData
{
    /**
     * formatData
     *
     * @param  mixed $orders
     * @return SupportCollection
     */
    public function formatData(LengthAwarePaginator|Collection $orders): SupportCollection
    {
        if ($orders instanceof LengthAwarePaginator) {
            $orders = $orders->getCollection();
        }

        return $orders->map(function ($order) {
            return [
                'id' => $order->id ?? null,
                'code' => $order->code ?? null,
                'created_by' => $order->created_by ?? null,
                'customer_name' => ($order->lastname ?? '') . ' ' . ($order->firstname ?? ''),
                'customer_phone' => $order->customer_phone ?? null,
                'customer_email' => $order->customer_email ?? null,
                'status' => $order->status ?? null,
                'shipping_address' => $order->shipping_address ?? null,
                'order_item' => $order->orderItem->map(function ($item) {
                    return [
                        'id' => $item->id ?? null,
                        'order_id' => $item->order_id ?? null,
                        'product_id' => $item->product->id ?? null,
                        'product_name' => $item->product->name ?? null,
                        'product_variant_id' => $item->product_variant->id ?? null,
                        'product_variant_name' => $item->product_variant->value ?? null,
                        'quantity' => $item->quantity ?? null,
                        'price' => $item->price ?? null,
                    ];
                }) ?? collect(),
            ];
        });
    }
}
