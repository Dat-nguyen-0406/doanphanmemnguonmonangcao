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
        $branchId = \App\Models\Branch::query()
            ->where('name', 'Aeon')
            ->value('id');

        if ($branchId === null) {
            return;
        }

        \App\Models\Restaurant::updateOrCreate(
            ['name' => 'Kichi Kichi Aeon'],
            [
                'branch_id' => $branchId,
                'cuisine_type' => 'Lẩu băng chuyền',
                'description' => 'Nhà hàng lẩu băng chuyền phong cách Nhật Bản.',
                'image_url' => 'https://example.com/kichi.jpg'
            ]
        );
    }
}
