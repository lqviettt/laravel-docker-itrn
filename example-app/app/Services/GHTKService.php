<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHTKService
{
    protected $apiUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->apiUrl = env('GHTK_API_URL');
        $this->apiToken = env('GHTK_API_TOKEN');
    }

    /**
     * calculateShippingFee
     *
     * @param  mixed $deliverProvince
     * @param  mixed $deliverDistrict
     * @param  mixed $weight
     * @param  mixed $value
     * @return void
     */
    public function calculateShippingFee($deliverProvince, $deliverDistrict, $deliverAddressDetail, $weight, $value)
    {
        $endpoint = '/services/shipment/fee';

        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get($this->apiUrl . $endpoint, [
            'pick_province' => config('services.ghtk.default_pick_province'),
            'pick_district' => config('services.ghtk.default_pick_district'),
            'pick_address' => config('services.ghtk.default_pick_address'),
            'province' => $deliverProvince,
            'district' => $deliverDistrict,
            'address' => $deliverAddressDetail,
            'weight' => $weight,
            'value' => $value,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to calculate shipping fee: ' . $response->body());
    }
}
