<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 5 danh mục mẫu
        $categories = ['Mỹ phẩm', 'Thời trang', 'Gia dụng', 'Thực phẩm', 'Sức khỏe'];
        foreach ($categories as $catName) {
            Category::updateOrCreate(['name' => $catName], ['slug' => Str::slug($catName)]);
        }

        // 2. Lấy danh sách ID hiện có để gán khóa ngoại
        $categoryIds = Category::pluck('id')->toArray();
        $branchIds = Branch::pluck('id')->toArray();
        // Chỉ lấy những User có role = 4 (Đối tác Shop)
        $partnerIds = User::where('role', 4)->pluck('id')->toArray();

        if (empty($branchIds) || empty($partnerIds)) {
            $this->command->error('Lỗi: Bạn cần tạo Chi nhánh (Branches) và User Role 4 trước khi chạy Seeder này!');
            return;
        }

        // 3. Tạo 50 sản phẩm
        for ($i = 1; $i <= 50; $i++) {
            $name = "Sản phẩm thử nghiệm " . $i;
            Product::create([
                'name'        => $name,
                'slug'        => Str::slug($name) . '-' . uniqid(),
                'description' => 'Mô tả chi tiết cho sản phẩm số ' . $i . '. Sản phẩm chất lượng cao từ AEON Mall.',
                'price'       => rand(50, 500) * 1000, // Giá từ 50k đến 500k
                'stock'       => rand(10, 100),
                'image'       => 'products/z5B5eDzpA5ILShM0VcMydeRWovrfZV5sEsX6HVr0.avif', // Đạt nhớ để 1 file ảnh mẫu ở public/storage/products/default.jpg
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'branch_id'   => $branchIds[array_rand($branchIds)],
                'user_id'     => $partnerIds[array_rand($partnerIds)],
                'is_active'   => 1, // Luôn để là 1 để tránh lỗi "Số lượng 0"
            ]);
        }

        $this->command->info('Đã tạo thành công 5 danh mục và 50 sản phẩm mẫu!');
    }
}