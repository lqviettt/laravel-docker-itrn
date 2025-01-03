<?php

namespace Modules\Order\Http\Controllers;

use App\Contract\OrderRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Order\Helpers\FormatData;
use Modules\Order\Models\Order;

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

        return $this->sendSuccess($formatData->formatData($query->paginate($perPage)));
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

            return $this->created($order);
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

        return $this->sendSuccess($order);
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

            return $this->updated($order->load('orderItem'));
        } catch (\Exception $e) {

            return $this->sendError($e->getMessage());
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
        $order->delete($order);

        return $this->deteled();
    }
}
