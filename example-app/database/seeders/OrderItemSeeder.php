<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\OrderItem;


class OrderItemSeeder extends Seeder
{
    public function run()
    {
        // Lấy tất cả sản phẩm đã tồn tại
        $products = Product::all();

        // Tạo 20 mục đơn hàng, đảm bảo rằng product_id tồn tại
        for ($i = 0; $i < 10; $i++) {
            OrderItem::factory()->create([
                'product_id' => $products->random()->id, // Chọn ngẫu nhiên một sản phẩm đã tồn tại
            ]);
        }
    }
}
