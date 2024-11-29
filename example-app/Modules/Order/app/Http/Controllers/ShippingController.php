<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingFeeRequest;
use App\Services\GHTKService;

class ShippingController extends Controller
{
    protected $ghtkService;

    /**
     * __construct
     *
     * @param  mixed $ghtkService
     * @return void
     */
    public function __construct(GHTKService $ghtkService)
    {
        $this->ghtkService = $ghtkService;
    }

    /**
     * calculateFee
     *
     * @param  mixed $request
     * @return void
     */
    public function calculateFee(ShippingFeeRequest $request)
    {
        try {
            $validateData = $request->validated();
            $fee = $this->ghtkService->calculateShippingFee($validateData);

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
