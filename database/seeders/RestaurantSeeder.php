<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Restaurant::create([
            'branch_id' => 1,
            'name' => 'Kichi Kichi Aeon',
            'cuisine_type' => 'Lẩu băng chuyền',
            'description' => 'Nhà hàng lẩu băng chuyền phong cách Nhật Bản.',
            'image_url' => 'https://example.com/kichi.jpg'
        ]);
    }
}
