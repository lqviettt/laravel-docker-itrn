<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHTKService
{
    /**
     * apiUrl
     *
     * @var mixed
     */
    protected $apiUrl;

    /**
     * apiToken
     *
     * @var mixed
     */
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
            'pick_province' => env('DEFAULT_PICK_PROVINCE'),
            'pick_district' => env('DEFAULT_PICK_DISTRICT'),
            'pick_address' => env('DEFAULT_PICK_ADDRESS'),
            'province' => $data['province'],
            'district' => $data['district'],
            'ward' => $data['ward'],
            'address' => $data['address'],
            'weight' => $data['weight'],
            'value' => $data['value'],
        ]);

        if ($response->successful()) {
            return $response->json();
            // return $response->json()['fee']['fee'] ?? null;
        }

        throw new \Exception('GHTK Error: ' . $response->body());
    }

    public function checkSupport() {}
}
