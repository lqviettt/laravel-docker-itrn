<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $code = $request->input('code');
        $status = $request->input('status');
        $search = $request->input('search');
        $productName = $request->input('productName');

        $order = Order::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customer_name', 'like', $search . '%')
                        ->orWhere('customer_phone', 'like', $search . '%')
                        ->orWhere('shipping_address', 'like', '%' . $search . '%');
                });
            })
            ->when($code, function ($query) use ($code) {
                return $query->where('code', $code);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($productName, function ($query) use ($productName) {
                return $query->whereHas('orderItem.product', function ($query) use ($productName) {
                    $query->where('name', 'like', '%' . $productName . '%');
                });
            })
            ->with('orderItem.product')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
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
                'code' => $request->code,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'status' => $request->status ?? 'default_status',
            ]);

            $orderItems = [];
            foreach ($request->order_items as $item) {
                $product = Product::find($item['product_id']);

                $orderItems[] = new OrderItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $product->quantity -= $item['quantity'];
                $product->save();
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
                'code' => $request->code,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'status' => $request->status,
            ]);

            // Nếu trạng thái là "hủy", hoàn lại số lượng cho sản phẩm
            if ($request->status === 'canceled') {
                foreach ($order->orderItem as $item) {
                    $product = Product::find($item['product_id']);

                    if ($product) {
                        $product->quantity += $item['quantity'];
                        $product->save();
                    }
                }

                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => 'canceled',
                    'description' => 'Order has been canceled, stock returned.',
                    'created_at' => now(),
                ]);
            }

            foreach ($request->order_items as $item) {
                $orderItems = $order->orderItem()->where('product_id', $item['product_id'])->first();

                if ($orderItems) {
                    //So luong chenh lech = sl moi - sl cu
                    $quantityDiff = $item['quantity'] - $orderItems->quantity;

                    $orderItems->update([
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);

                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->quantity -= $quantityDiff;
                        $product->save();
                    }
                } else {
                    $order->orderItem()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);

                    $product = Product::find($item['product_id']);
                    $product->quantity -= $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();

            return response()->json($order->load('orderItem'));
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage());
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
