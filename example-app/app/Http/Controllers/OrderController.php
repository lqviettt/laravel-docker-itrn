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
        try {
            DB::beginTransaction();

            $order = Order::create($request->storeOrder());
            $productIds = collect($request->order_items)->pluck('product_id')->toArray();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $orderItems = [];
            $quantitiesToUpdate = [];

            foreach ($request->order_items as $item) {
                $product = $products->get($item['product_id']);

                if ($product) {
                    $orderItems[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ];

                    $quantitiesToUpdate[$item['product_id']] = $product->quantity - $item['quantity'];
                }
            }

            foreach ($quantitiesToUpdate as $productId => $newQuantity) {
                $products[$productId]->update(['quantity' => $newQuantity]);
            }

            $order->orderItem()->createMany($orderItems);

            DB::commit();

            return response()->json($order);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
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
        if ($order->status === 'canceled') {
            return response()->json([
                'error' => 'Cannot update an order that has been canceled.'
            ], 400);
        }

        try {
            DB::beginTransaction();
            
            $order->update(['status' => $request->status]);
            $oldOrderItems = $order->orderItem()->with('product')->get();
            $oldItemsByProductId = $oldOrderItems->keyBy('product_id');
            $productIds = collect($request->order_items)->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            if ($request->status === 'canceled') {
                OrderHelper::cancelOrder($order, $oldOrderItems);
            } else {
                $order->update($request->updateOrder());
                OrderHelper::updateOrderItems($request, $oldItemsByProductId, $products, $order);
                OrderHelper::removeDeletedItems($oldOrderItems, $request);
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
