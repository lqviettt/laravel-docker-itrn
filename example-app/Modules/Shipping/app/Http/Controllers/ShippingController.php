<?php

namespace Modules\Shipping\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShippingFeeRequest;
use App\Services\GHNService;
use App\Services\GHTKService;
use Modules\Shipping\Models\Location;

class ShippingController extends Controller
{
    /**
     * ghtkService
     *
     * @var GHTKService
     */
    protected $ghtkService;

    /**
     * ghnService
     *
     * @var GHNService
     */
    protected $ghnService;

    /**
     * __construct
     *
     * @param  GHTKService $ghtkService
     * @param  GHNService $ghnService
     * @return void
     */
    public function __construct(GHTKService $ghtkService, GHNService $ghnService)
    {
        $this->ghtkService = $ghtkService;
        $this->ghnService = $ghnService;
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
            $location = $this->getLocation($validateData['province'], $validateData['district'], $validateData['ward']);

            if (!$location['province'] || !$location['district'] || !$location['ward']) {
                return response()->json(['error' => 'Địa chỉ không hợp lệ'], 400);
            }

            $ghtkFee = $this->ghtkService->calculateShippingFee($validateData);
            $ghnFee = $this->ghnService->calculateShippingFee($location['district'], $location['ward'], $validateData);

            return response()->json([
                'success' => true,
                'GHTK' => $ghtkFee,
                'GHN' => $ghnFee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // /**
    //  * calculateFee2
    //  *
    //  * @param  mixed $request
    //  * @return void
    //  */
    // public function calculateFee(ShippingFeeRequest $request)
    // {
    //     try {
    //         $validateData = $request->validated();
    //         $location = $this->getLocation($validateData['province'], $validateData['district'], $validateData['ward']);

    //         if (!$location['province'] || !$location['district'] || !$location['ward']) {
    //             return response()->json(['error' => 'Địa chỉ không hợp lệ'], 400);
    //         }

    //         $ghtkFee = null;
    //         $ghnFee = null;
    //         $ghtkSupported = false;
    //         $ghnSupported = false;

    //         $ghtkSupported = $this->ghtkService->checkSupport($validateData);
    //         if ($ghtkSupported) {
    //             $ghtkFee = $this->ghtkService->calculateShippingFee($validateData);
    //         }

    //         if ($ghtkSupported) {
    //             $ghnSupported = $this->ghnService->checkSupport($location['district'], $location['ward'], $validateData);
    //             if ($ghnSupported) {
    //                 $ghnFee = $this->ghnService->calculateShippingFee($location['district'], $location['ward'], $validateData);
    //             }
    //         }

    //         if (!$ghtkSupported && !$ghnSupported) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Cả hai dịch vụ đều không hỗ trợ địa chỉ này.',
    //             ], 400);
    //         }

    //         $response = [
    //             'success' => true,
    //         ];

    //         if ($ghtkSupported) {
    //             $response['GHTK'] = $ghtkFee;
    //         } else {
    //             $response['message'] = 'GHTK không hỗ trợ địa chỉ này';
    //         }

    //         if ($ghnSupported) {
    //             $response['GHN'] = $ghnFee;
    //         } else {
    //             $response['message'] = 'GHN không hỗ trợ địa chỉ này';
    //         }

    //         return response()->json($response);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ], 400);
    //     }
    // }

    /**
     * getLocation
     *
     * @param  mixed $provinceName
     * @param  mixed $districtName
     * @param  mixed $wardName
     * @return void
     */
    private function getLocation($provinceName, $districtName, $wardName)
    {
        $province = Location::where('name', 'like',  $provinceName . '%')
            ->where('type', 'province')
            ->first();

        $district = Location::where('name', 'like', '%' . $districtName . '%')
            ->where('type', 'district')
            ->where('parent_id', $province ? $province->id : null)
            ->first();

        $ward = Location::where('name', 'like', '%' . $wardName . '%')
            ->where('type', 'ward')
            ->where('parent_id', $district ? $district->id : null)
            ->first();

        return [
            'province' => $province,
            'district' => $district,
            'ward' => $ward,
        ];
    }
}
