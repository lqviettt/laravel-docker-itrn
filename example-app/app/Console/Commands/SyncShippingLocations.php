<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Modules\Shipping\Models\Location;

class SyncShippingLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-shipping-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ danh sách tỉnh/quận/xã từ GHN';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncGHN();
        $this->info('Đồng bộ dữ liệu địa phương thành công!');
    }

    /**
     * syncGHN
     *
     * @return void
     */
    private function syncGHN()
    {
        $token = env('GHN_API_TOKEN');

        $response = Http::timeout(60)->withHeaders(['Token' => $token])->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');

        if (!$response->successful()) {
            $this->error('Lỗi khi lấy tỉnh từ API: ' . $response->body());
            return;
        }

        $provinces = $response->json()['data'];

        foreach ($provinces as $province) {
            $provinceModel = Location::updateOrCreate([
                'name' => $province['ProvinceName'],
                'type' => 'province',
            ], [
                'code' => $province['ProvinceID'],
            ]);

            $districtsResponse = Http::timeout(60)->withHeaders(['Token' => $token])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                    'province_id' => $province['ProvinceID']
                ]);

            if (!$districtsResponse->successful()) {
                $this->error('Lỗi khi lấy quận từ API cho tỉnh: ' . $province['ProvinceName']);
                continue;
            }

            $districts = $districtsResponse->json()['data'];
            $this->info('Đang đồng bộ quận cho tỉnh: ' . $province['ProvinceName']);

            foreach ($districts as $district) {
                $districtModel = Location::updateOrCreate([
                    'name' => $district['DistrictName'],
                    'type' => 'district',
                    'parent_id' => $provinceModel->id,
                ], [
                    'code' => $district['DistrictID'],
                ]);

                $wardsResponse = Http::timeout(60)->withHeaders(['Token' => $token])
                    ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                        'district_id' => $district['DistrictID']
                    ]);

                if (!$wardsResponse->successful()) {
                    $this->error('Lỗi khi lấy xã từ API cho quận: ' . $district['DistrictName']);
                    continue;
                }

                $wards = $wardsResponse->json()['data'];

                if (empty($wards)) {
                    $this->info('Không có xã/phường nào cho quận: ' . $district['DistrictName']);
                } else {
                    foreach ($wards as $ward) {
                        Location::updateOrCreate([
                            'name' => $ward['WardName'],
                            'type' => 'ward',
                            'parent_id' => $districtModel->id,
                        ], [
                            'code' => $ward['WardCode'],
                        ]);
                    }
                }
            }
        }
    }
}
