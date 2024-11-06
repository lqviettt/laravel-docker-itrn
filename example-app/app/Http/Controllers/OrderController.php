<?php

namespace App\Http\Controllers;

use App\Helpers\FormatData;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * __construct
     *
     * @param  OrderRepositoryInterface $orderRepository
     * @return void
     */
    public function __construct(protected OrderRepositoryInterface $orderRepository) {}

    /**
     * index
     *
     * @param  Request $request
     * @param  FormatData $formatData
     * @return void
     */
    public function index(Request $request, FormatData $formatData): JsonResponse
    {
        $perPage = $request->input('perPage', 5);
        $this->authorize('view', Order::class);
        $query = $this->orderRepository
            ->builderQuery()
            ->searchByStatus($request->status)
            ->searchByNameCode($request->search)
            ->searchByPhone($request->phone)
            ->searchByCreated($request->created_by);

        return response()->json($formatData->formatData($query->paginate($perPage)));
    }

    /**
     * store
     *
     * @param  OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);
        return DB::transaction(function () use ($request) {
            $order = $this->orderRepository
                ->createOrder($request->storeOrder(), $request->order_items);

            return response()->json($order);
        });
    }

    /**
     * show
     *
     * @param  mixed $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        $order = $this->orderRepository->find($order);

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
        $this->authorize('update', $order);
        if ($order->status === 'canceled') {
            return response()->json([
                'error' => 'Cannot update a canceled order.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($request, $order) {
                $this->orderRepository->updateOrder($order, $request->updateOrder());
            });

            return response()->json($order->load('orderItem'));
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);
        $order = $this->orderRepository->delete($order);

        return response()->json($order);
    }
}
