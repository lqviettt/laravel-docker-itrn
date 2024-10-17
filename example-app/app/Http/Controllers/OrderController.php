<?php

namespace App\Http\Controllers;

use App\Helpers\FormatData;
use App\Helpers\OrderHelper;
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
    public function __construct(private FormatData $formatData) {}

    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(Request $request,): JsonResponse
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $order = Order::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like',  $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->with('orderItem.product')
            ->get();

        return response()->json($this->formatData->formatData($order));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $order = Order::create($request->storeOrder());
            $order->products()->attach($request->order_items);

            foreach ($request->order_items as $item) {
                $order->products()->where('products.id', $item['product_id'])
                    ->decrement('products.quantity', $item['quantity']);
            }

            return response()->json($order);
        }, 5);
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
        if ($order->status === 'canceled') {
            return response()->json([
                'error' => 'Cannot update a canceled order.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($request, $order) {
                $order->update(['status' => $request->status]);
                $oldItems = $order->orderItem()->with('product')->get();
                $oldItemsByProductId = $oldItems->keyBy('product_id');
                $orderItems = collect($request->order_items);

                if ($request->status === 'canceled') {
                    OrderHelper::cancelOrder($order, $oldItems);
                } else {
                    $order->update($request->updateOrder());
                    OrderHelper::updateOrderItems($orderItems, $oldItemsByProductId, $order);
                    OrderHelper::removeDeletedItems($oldItems, $orderItems);
                }
            });

            return response()->json($order->load('orderItem'));
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);
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
