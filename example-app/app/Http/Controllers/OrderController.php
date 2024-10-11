<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $order = Order::with(['orderItem.product'])->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'status' => $order->status,
                'shipping_address' => $order->shipping_address,
                'order_item' => $order->orderItem->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'order_id' => $item->order_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
            ];
        });

        return response()->json($order);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'status' => $request->status
            ]);

            $orderItems = [];
            foreach ($request->order_items as $item) {
                $orderItems[] = new OrderItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            $order->orderItem()->saveMany($orderItems);

            DB::commit();

            return response()->json($order);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage());
        }
    }

    /**
     * show
     *
     * @param  mixed $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        $order->load('orderItem.product');

        return response()->json($order);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $order
     * @return JsonResponse
     */
    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        try {
            DB::beginTransaction();

            $order->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'status' => $request->status,
            ]);

            foreach ($request->order_items as $item) {
                $orderItems = $order->orderItem()->where('product_id', $item['product_id'])->first();

                if ($orderItems) {
                    $orderItems->update([
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                } else {
                    $order->orderItem()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $order
     * @return JsonResponse
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json($order);
    }
}
