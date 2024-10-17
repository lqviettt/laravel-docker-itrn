<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class CategoryWithProductSeeder extends Seeder
{
    public function run()
    {
        // Tạo 1000 category trước
        $categories = Category::factory()->count(100)->create();
        $categoryIds = $categories->pluck('id')->toArray();
    
        // Tạo số lượng sản phẩm mong muốn
        $totalProducts = 1000000; // Tổng số sản phẩm cần tạo
        $batchSize = 1000; // Số lượng sản phẩm mỗi lần chèn
    
        for ($i = 0; $i < $totalProducts; $i += $batchSize) {
            $products = [];
    
            for ($j = 0; $j < $batchSize; $j++) {
                $randomCategoryId = $categoryIds[array_rand($categoryIds)];
                $products[] = [
                    'category_id' => $randomCategoryId,
                    'name' => 'Product ' . ($i + $j + 1),
                    'description' => 'Description for product ' . ($i + $j + 1),
                    'code' => 'CODE-' . ($i + $j + 1),
                    'price' => rand(100, 1000),
                    'quantity' => rand(1, 100),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            // Sử dụng DB::table để chèn nhiều bản ghi cùng một lúc
            DB::table('products')->insert($products);
        }
    }
    
}
