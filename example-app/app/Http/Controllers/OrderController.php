<?php

namespace App\Http\Controllers;

use App\Helpers\FormatData;
use App\Http\Requests\OrderRequest;
use App\Mail\OrderSuccessfulMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
    public function index(Request $request, FormatData $formatData)
    {
        $query = $this->orderRepository
            ->builderQuery()
            ->searchByStatus($request->status)
            ->searchByNameCode($request->search)
            ->searchByPhone($request->phone)
            ->searchByCreated($request->created_by);

        return response()->json($formatData->formatData($query->paginate(10)));
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
        $order = $this->orderRepository->delete($order);

        return response()->json($order);
    }
}
