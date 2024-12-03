<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHNService
{
    /**
     * baseUrl
     *
     * @var mixed
     */
    protected $baseUrl;

    /**
     * token
     *
     * @var mixed
     */
    protected $token;

    public function __construct()
    {
        $this->baseUrl = env('GHN_API_URL');
        $this->token = env('GHN_API_TOKEN');
    }

    /**
     * calculateShippingFee
     *
     * @param  mixed $district
     * @param  mixed $ward
     * @param  mixed $data
     * @return void
     */
    public function calculateShippingFee($district, $ward, array $data)
    {
        $response = Http::withHeaders([
            'Token' => $this->token,
            'shop_id' => env('GHN_SHOP_ID'),
        ])->post("{$this->baseUrl}/v2/shipping-order/fee", [
            'from_district_id' => (int) env('FROM_DISTRICT_ID'),
            'to_district_id' => (int) $district->code,
            'to_ward_code' => (string) $ward->code,
            'weight' => $data['weight'],
            'insurance_value' => $data['value'],
            'service_type_id' => $data['service_type_id'],
        ]);

        if ($response->successful()) {
            return $response->json();
            // return $response->json()['data']['total'] ?? null;
        }

        throw new \Exception('GHN Error: ' . $response->body());
    }
}
