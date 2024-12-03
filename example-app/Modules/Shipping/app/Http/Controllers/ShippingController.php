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
