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
                    $query->where('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $search . '%')
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
            // Kiểm tra trạng thái hiện tại của đơn hàng, nếu là hủy thì không cho phép cập nhật
            if ($order->status === 'canceled') {
                return response()->json([
                    'error' => 'Cannot update an order that has been canceled.'
                ], 400);
            }
            DB::beginTransaction();

            // Lưu lại thông tin chi tiết đơn hàng cũ
            $oldOrderItems = $order->orderItem()->get();

            // Cập nhật thông tin đơn hàng
            $order->update([
                'code' => $request->code,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'status' => $request->status,
            ]);

            // Nếu trạng thái mới là "hủy", hoàn lại số lượng cho tất cả các sản phẩm cũ
            if ($request->status === 'canceled') {
                foreach ($oldOrderItems as $item) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        // Hoàn lại số lượng sản phẩm vào kho
                        $product->quantity += $item->quantity;
                        $product->save();
                    }
                }

                // Ghi lại lịch sử đơn hàng khi bị hủy
                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => 'canceled',
                    'description' => 'Order has been canceled, stock returned.',
                    'created_at' => now(),
                ]);
            } else {
                // Nếu trạng thái không phải là "hủy", xử lý cập nhật thông tin sản phẩm
                $oldItemsByProductId = $oldOrderItems->keyBy('product_id');

                foreach ($request->order_items as $item) {
                    $product = Product::find($item['product_id']);
                    if (!$product) continue; // Nếu sản phẩm không tồn tại trong products thì bỏ qua

                    // Kiểm tra xem sản phẩm này có trong đơn hàng cũ hay không, nếu tồn tại
                    if (isset($oldItemsByProductId[$item['product_id']])) {
                        $oldOrderItem = $oldItemsByProductId[$item['product_id']];

                        // So sánh số lượng cũ và mới để cập nhật kho
                        $quantityDifference = $item['quantity'] - $oldOrderItem->quantity;

                        // Nếu số lượng mới lớn hơn, trừ đi số lượng từ kho
                        if ($quantityDifference > 0) {
                            $product->quantity -= $quantityDifference;
                        }
                        // Nếu số lượng mới nhỏ hơn, hoàn lại số lượng vào kho
                        else if ($quantityDifference < 0) {
                            $product->quantity += abs($quantityDifference);
                        }

                        $product->save();

                        // Cập nhật chi tiết đơn hàng
                        $oldOrderItem->update([
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ]);
                    } else {
                        // Nếu sản phẩm không có trong đơn hàng cũ, trừ số lượng tương ứng của sản phẩm từ kho
                        $product->quantity -= $item['quantity'];
                        $product->save();

                        // Thêm sản phẩm mới vào chi tiết đơn hàng
                        $order->orderItem()->create([
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ]);
                    }
                }

                // Xóa sản phẩm đã bị loại bỏ khỏi đơn hàng
                foreach ($oldOrderItems as $oldItem) {
                    if (!collect($request->order_items)->pluck('product_id')->contains($oldItem->product_id)) {
                        // Hoàn lại số lượng vào kho cho sản phẩm bị loại bỏ
                        $product = Product::find($oldItem->product_id);
                        if ($product) {
                            $product->quantity += $oldItem->quantity;
                            $product->save();
                        }

                        // Xóa chi tiết đơn hàng này
                        $oldItem->delete();
                    }
                }
            }

            DB::commit();

            return response()->json($order->load('orderItem'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
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
