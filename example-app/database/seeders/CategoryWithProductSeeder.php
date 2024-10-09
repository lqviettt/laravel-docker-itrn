<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CategoryWithProductSeeder extends Seeder
{
    public function run()
    {
        // Tạo 1000 category trước
        $categories = Category::factory()->count(100)->create();

        // Lấy danh sách tất cả category_id đã tồn tại
        $categoryIds = $categories->pluck('id')->toArray();

        // Tạo số lượng sản phẩm mong muốn
        $totalProducts = 10000; // Tổng số sản phẩm cần tạo

        for ($i = 0; $i < $totalProducts; $i++) {
            // Chọn ngẫu nhiên một category_id từ danh sách đã lấy
            $randomCategoryId = $categoryIds[array_rand($categoryIds)];

            // Tạo sản phẩm với category_id hợp lệ
            Product::factory()->create([
                'category_id' => $randomCategoryId,
                // Bạn có thể thêm các trường khác ở đây
            ]);
        }
    }
}
