<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingFeeRequest;
use App\Services\GHTKService;

class ShippingController extends Controller
{
    protected $ghtkService;

    public function __construct(GHTKService $ghtkService)
    {
        $this->ghtkService = $ghtkService;
    }

    public function calculateFee(ShippingFeeRequest $request)
    {
        try {
            $validateData = $request->validated();
            $fee = $this->ghtkService->calculateShippingFee(
                $validateData['shipping_province'],
                $validateData['shipping_district'],
                $validateData['shipping_address_detail'],
                $validateData['total_weight'],
                $validateData['total_price']
            );

            return response()->json([
                'success' => true,
                'fee' => $fee['fee'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
