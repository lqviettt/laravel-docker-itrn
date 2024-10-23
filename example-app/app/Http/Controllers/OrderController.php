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
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request, FormatData $formatData)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $created_by = $request->input('created_by');

        $order = $this->orderRepository->all($search, $status, $created_by);

        return response()->json($formatData->formatData($order));
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
            $order = $this->orderRepository
                ->create($request->storeOrder(), $request->order_items);

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
        if ($order->status === 'canceled') {
            return response()->json([
                'error' => 'Cannot update a canceled order.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($request, $order) {
                $this->orderRepository->update($order, $request->updateOrder());
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
        $order = $this->orderRepository->delete($order);

        return response()->json($order);
    }
}
