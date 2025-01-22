<?php

namespace Modules\Order\Http\Controllers;

use App\Contract\OrderRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Jobs\SendOrderEmailJob;
use Carbon\Carbon;
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
        $perPage = $request->input('perPage', 7);
        // $this->authorize('view', Order::class);
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
        // $this->authorize('create', Order::class);
        $result = DB::transaction(function () use ($request) {
            $order = $this->orderRepository
                ->createOrder($request->storeOrder(), $request->order_item);

            if ($order->customer_email) {
                SendOrderEmailJob::dispatch($order);
            }

            return $order;
        });

        if ($result->payment_method == 'COD') {
            return $this->created($result);
        } else {
            $config = $this->fetchVNPay();
            $vnpUrl = $this->generateUrlPayment(
                $result->payment_method,
                $result,
                $config
            );
            $result->payment = $vnpUrl;

            return $this->created($result);
        }
    }

    //Doan nay tam thoi de day, sau nay se chuyen vao service
    private function fetchVNPay(): array
    {
        return [
            'return_url' => config('payment-method.vnpay.return_url'),
            'refund_url' => config('payment-method.vnpay.refund_url'),
            'refund_email' => config('payment-method.vnpay.refund_email'),
            'tmn_code' => config('payment-method.vnpay.tmn_code'),
            'url' => config('payment-method.vnpay.url'),
            'secret_key' => config('payment-method.vnpay.secret_key'),
        ];
    }

    //Doan nay tam thoi de day, sau nay se chuyen vao service
    private function generateUrlPayment(string $vnpBankCode, Order $order, array $config): array
    {
        $vnpHashSecret = $config['secret_key'];
        $vnpUrl = $config['url'];
        $vnpIpAddr = request()->ip();
        $vnpCreateDate = Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis');
        $vnpExpireDate = Carbon::now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis');
        $totalPayment = $order->total_price;
        $txnRef = $order->code;

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $config['tmn_code'],
            "vnp_Amount" => $totalPayment * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnpCreateDate,
            "vnp_ExpireDate" => $vnpExpireDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnpIpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan GD: " . $txnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $config['return_url'],
            "vnp_TxnRef" => $txnRef,
        ];

        $filteredData = array_filter(
            $inputData,
            function ($key) {
                return !in_array($key, ['vnp_Version', 'vnp_TmnCode', 'vnp_ReturnUrl', 'vnp_TxnRef']);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($vnpBankCode)) {
            $inputData['vnp_BankCode'] = $vnpBankCode;
        }

        ksort($inputData);
        $query = "";
        $index = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($index == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $index = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpUrl = $vnpUrl . "?" . $query;
        if (isset($vnpHashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);
            $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
            return array_merge(
                ['payment_url' => $vnpUrl],
                $filteredData
            );
        }
        return false;
    }

    /**
     * show
     *
     * @param  mixed $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        // $this->authorize('view', $order);
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
        // $this->authorize('update', $order);
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
        // $this->authorize('delete', $order);
        $order->delete($order);

        return $this->deteled();
    }
}
