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

    // /**
    //  * syncGHN
    //  *
    //  * @return void
    //  */
    // private function syncGHN()
    // {
    //     $token = env('GHN_API_TOKEN');

    //     $response = Http::timeout(120)->withHeaders(['Token' => $token])->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');

    //     if (!$response->successful()) {
    //         $this->error('Lỗi khi lấy tỉnh từ API: ' . $response->body());
    //         return;
    //     }

    //     $provinces = $response->json()['data'];

    //     foreach ($provinces as $province) {
    //         $provinceModel = Location::updateOrCreate([
    //             'name' => $province['ProvinceName'],
    //             'type' => 'province',
    //         ], [
    //             'code' => $province['ProvinceID'],
    //         ]);

    //         $districtsResponse = Http::timeout(120)->withHeaders(['Token' => $token])
    //             ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
    //                 'province_id' => $province['ProvinceID']
    //             ]);

    //         if (!$districtsResponse->successful()) {
    //             $this->error('Lỗi khi lấy quận từ API cho tỉnh: ' . $province['ProvinceName']);
    //             continue;
    //         }

    //         $districts = $districtsResponse->json()['data'];
    //         $this->info('Đang đồng bộ quận cho tỉnh: ' . $province['ProvinceName']);

    //         foreach ($districts as $district) {
    //             $districtModel = Location::updateOrCreate([
    //                 'name' => $district['DistrictName'],
    //                 'type' => 'district',
    //                 'parent_id' => $provinceModel->id,
    //             ], [
    //                 'code' => $district['DistrictID'],
    //             ]);

    //             $wardsResponse = Http::timeout(120)->withHeaders(['Token' => $token])
    //                 ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
    //                     'district_id' => $district['DistrictID']
    //                 ]);

    //             if (!$wardsResponse->successful()) {
    //                 $this->error('Lỗi khi lấy xã từ API cho quận: ' . $district['DistrictName']);
    //                 continue;
    //             }

    //             $wards = $wardsResponse->json()['data'];

    //             if (empty($wards)) {
    //                 $this->info('Không có xã/phường nào cho quận: ' . $district['DistrictName']);
    //             } else {
    //                 foreach ($wards as $ward) {
    //                     Location::updateOrCreate([
    //                         'name' => $ward['WardName'],
    //                         'type' => 'ward',
    //                         'parent_id' => $districtModel->id,
    //                     ], [
    //                         'code' => $ward['WardCode'],
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    // }

    private function syncGHN()
    {
        $token = env('GHN_API_TOKEN');
        $provincesResponse = Http::timeout(60)->withHeaders(['Token' => $token])
            ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');

        if (!$provincesResponse->successful()) {
            $this->error('Lỗi khi lấy tỉnh từ API: ' . $provincesResponse->body());
            return;
        }

        $provinces = $provincesResponse->json()['data'];
        $existingProvinces = Location::where('type', 'province')->pluck('name')->toArray(); // Lấy danh sách tên tỉnh hiện có trong DB
        $existingDistricts = Location::where('type', 'district')->pluck('name', 'parent_id')->toArray(); // Lấy danh sách quận theo tỉnh
        $existingWards = Location::where('type', 'ward')->pluck('name', 'parent_id')->toArray(); // Lấy danh sách xã/phường theo quận

        $provincesToInsert = [];
        $districtsToInsert = [];
        $wardsToInsert = [];

        foreach ($provinces as $province) {
            // Kiểm tra xem tỉnh đã có trong cơ sở dữ liệu chưa
            if (in_array($province['ProvinceName'], $existingProvinces)) {
                continue; // Nếu đã có thì bỏ qua
            }

            // Lưu tỉnh
            $provinceData = [
                'name' => $province['ProvinceName'],
                'type' => 'province',
                'code' => $province['ProvinceID'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $provincesToInsert[] = $provinceData;

            // Lấy quận của tỉnh
            $districtsResponse = Http::timeout(60)->withHeaders(['Token' => $token])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                    'province_id' => $province['ProvinceID']
                ]);

            if (!$districtsResponse->successful()) {
                $this->error('Lỗi khi lấy quận từ API cho tỉnh: ' . $province['ProvinceName']);
                continue;
            }

            $districts = $districtsResponse->json()['data'];

            foreach ($districts as $district) {
                // Kiểm tra xem quận đã có trong cơ sở dữ liệu chưa
                if (isset($existingDistricts[$province['ProvinceID']]) && in_array($district['DistrictName'], $existingDistricts[$province['ProvinceID']])) {
                    continue; // Nếu quận đã có thì bỏ qua
                }

                // Lưu quận
                $districtData = [
                    'name' => $district['DistrictName'],
                    'type' => 'district',
                    'code' => $district['DistrictID'],
                    'parent_id' => null, // Sẽ cập nhật parent_id sau khi chèn tỉnh
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $districtsToInsert[] = $districtData;

                // Lấy xã của quận
                $wardsResponse = Http::timeout(60)->withHeaders(['Token' => $token])
                    ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                        'district_id' => $district['DistrictID']
                    ]);

                if (!$wardsResponse->successful()) {
                    $this->error('Lỗi khi lấy xã từ API cho quận: ' . $district['DistrictName']);
                    continue;
                }

                $wards = $wardsResponse->json()['data'];
                foreach ($wards as $ward) {
                    // Kiểm tra xem xã đã có trong cơ sở dữ liệu chưa
                    if (isset($existingWards[$district['DistrictID']]) && in_array($ward['WardName'], $existingWards[$district['DistrictID']])) {
                        continue; // Nếu xã/phường đã có thì bỏ qua
                    }

                    $wardData = [
                        'name' => $ward['WardName'],
                        'type' => 'ward',
                        'code' => $ward['WardCode'],
                        'parent_id' => null, // Sẽ cập nhật parent_id sau khi chèn quận
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $wardsToInsert[] = $wardData;
                }
            }
        }

        // Batch insert
        if (!empty($provincesToInsert)) {
            Location::insert($provincesToInsert);
            $this->info('Đã đồng bộ xong tỉnh.');
        }

        if (!empty($districtsToInsert)) {
            Location::insert($districtsToInsert);
            $this->info('Đã đồng bộ xong quận.');
        }

        if (!empty($wardsToInsert)) {
            Location::insert($wardsToInsert);
            $this->info('Đã đồng bộ xong xã/phường.');
        }
    }
}
