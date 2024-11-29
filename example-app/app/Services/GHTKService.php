<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHTKService
{
    protected $apiUrl;
    protected $apiToken;

    /**
     * __construct
     *
     * @return void
     */
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
    public function calculateShippingFee(array $data)
    {
        $response = Http::withHeaders([
            'Token' => $this->apiToken,
            'Content-Type' => 'application/json',
        ])->get("{$this->apiUrl}/services/shipment/fee", [
            'pick_province' => config('services.ghtk.default_pick_province'),
            'pick_district' => config('services.ghtk.default_pick_district'),
            'pick_address' => config('services.ghtk.default_pick_address'),
            'province' => $data['shipping_province'],
            'district' => $data['shipping_district'],
            'address' => $data['shipping_address_detail'],
            'weight' => $data['total_weight'],
            'value' => $data['total_price'],
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to calculate shipping fee: ' . $response->body());
    }
}
