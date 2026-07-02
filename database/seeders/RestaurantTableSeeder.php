<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\RestaurantTable;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách tất cả các nhà hàng đang có trong Database
        $restaurants = Restaurant::all();

        // Nếu chưa có nhà hàng nào, dừng lại và báo lỗi
        if ($restaurants->isEmpty()) {
            $this->command->error('Chưa có nhà hàng nào. Vui lòng chạy RestaurantSeeder trước!');
            return;
        }

        // Tạo một kịch bản danh sách bàn mẫu
        $tableTemplates = [
            ['table_number' => 'Bàn Đôi 01', 'capacity' => 2],
            ['table_number' => 'Bàn Đôi 02', 'capacity' => 2],
            ['table_number' => 'Bàn Tiêu chuẩn 01', 'capacity' => 4],
            ['table_number' => 'Bàn Tiêu chuẩn 02', 'capacity' => 4],
            ['table_number' => 'Bàn Tiêu chuẩn 03', 'capacity' => 4],
            ['table_number' => 'Bàn Gia đình 01', 'capacity' => 6],
            ['table_number' => 'Bàn Gia đình 02', 'capacity' => 8],
            ['table_number' => 'Bàn VIP (Phòng riêng)', 'capacity' => 12],
            ['table_number' => 'Bàn Tiệc siêu lớn', 'capacity' => 20],
        ];

        // Lặp qua từng nhà hàng và nạp danh sách bàn mẫu này vào cho nó
        foreach ($restaurants as $restaurant) {
            foreach ($tableTemplates as $table) {
                // Dùng updateOrCreate để không bị tạo trùng lặp nếu chạy lệnh nhiều lần
                RestaurantTable::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'table_number' => $table['table_number'],
                    ],
                    [
                        'capacity' => $table['capacity'],
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Đã seed thành công danh sách Bàn cho tất cả các Nhà hàng!');
    }
}
