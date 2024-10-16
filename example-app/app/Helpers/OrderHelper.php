<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\OrderHistory;
use Illuminate\Support\Collection;

class OrderHelper
{
    public static function cancelOrder($order, Collection $oldOrderItems)
    {
        // Hoàn lại số lượng cho tất cả các sản phẩm cũ
        $oldOrderItems->each(function ($item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('quantity', $item->quantity);
            }
        });

        // Ghi lại lịch sử đơn hàng khi bị hủy
        OrderHistory::create([
            'order_id' => $order->id,
            'status' => 'canceled',
            'description' => 'Order has been canceled, stock returned.',
            'created_at' => now(),
        ]);
    }

    public static function updateOrderItems($request, $oldItemsByProductId, $products, $order)
    {
        collect($request->order_items)->each(function ($item) use ($oldItemsByProductId, $products, $order) {
            $product = $products->get($item['product_id']);
            if ($product) {
                $oldOrderItem = $oldItemsByProductId->get($item['product_id']);

                // Tính toán số lượng thay đổi
                if ($oldOrderItem) {
                    // Nếu đã tồn tại trong đơn hàng, tính số lượng mới
                    $quantityChange = $item['quantity'] - $oldOrderItem->quantity;

                    // Cập nhật số lượng sản phẩm trong kho
                    $product->decrement('quantity', $quantityChange);

                    // Cập nhật chi tiết đơn hàng
                    $oldOrderItem->update([
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                } else {
                    // Nếu là sản phẩm mới trong đơn hàng, trừ số lượng từ kho
                    $product->decrement('quantity', $item['quantity']);
                    $order->orderItem()->create($item);
                }
            }
        });
    }

    public static function removeDeletedItems($oldOrderItems, $request)
    {
        $oldOrderItems->filter(function ($oldItem) use ($request) {
            return !collect($request->order_items)->pluck('product_id')->contains($oldItem->product_id);
        })->each(function ($oldItem) {
            // Hoàn lại số lượng vào kho cho sản phẩm bị loại bỏ
            $oldItem->product->increment('quantity', $oldItem->quantity);
            $oldItem->delete();
        });
    }
}
