<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\RestaurantMenuItem;

class RestaurantMenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả nhà hàng
        $restaurants = Restaurant::all();

        if ($restaurants->isEmpty()) {
            $this->command->error('Chưa có nhà hàng nào. Vui lòng chạy RestaurantSeeder trước!');
            return;
        }

        // Định nghĩa thực đơn mẫu cho từng nhà hàng dựa theo tên
        $menus = [
            'Kichi Kichi' => [
                // Khai vị
                ['name' => 'Kimbap chiên', 'category' => 'appetizer', 'price' => 49000, 'description' => 'Cơm cuộn Hàn Quốc tẩm bột chiên xù giòn rụm.'],
                ['name' => 'Khoai tây chiên sốt bơ tỏi', 'category' => 'appetizer', 'price' => 39000, 'description' => 'Khoai tây chiên giòn lắc bơ tỏi thơm lừng.'],
                // Món chính (Combo / Buffets)
                ['name' => 'Combo Buffet lẩu băng chuyền (Người lớn)', 'category' => 'main', 'price' => 269000, 'description' => 'Buffet lẩu băng chuyền không giới hạn bò Mỹ, hải sản và các món nhúng.'],
                ['name' => 'Combo Buffet lẩu băng chuyền (Trẻ em)', 'category' => 'main', 'price' => 129000, 'description' => 'Buffet lẩu băng chuyền dành cho trẻ em cao từ 1m đến 1m3.'],
                // Tráng miệng
                ['name' => 'Chè khúc bạch', 'category' => 'dessert', 'price' => 29000, 'description' => 'Chè khúc bạch ngọt thanh mát lạnh với quả vải và hạnh nhân.'],
                ['name' => 'Kem matcha Nhật Bản', 'category' => 'dessert', 'price' => 25000, 'description' => 'Kem vị trà xanh nguyên chất mát lạnh.'],
                // Đồ uống
                ['name' => 'Nước ép dưa hấu', 'category' => 'drink', 'price' => 35000, 'description' => 'Nước ép dưa hấu nguyên chất thơm mát.'],
                ['name' => 'Trà đào sả đá', 'category' => 'drink', 'price' => 29000, 'description' => 'Trà đào tươi mát kết hợp với hương thơm của sả.'],
            ],
            'GoGi House' => [
                ['name' => 'Salad hoa quả', 'category' => 'appetizer', 'price' => 59000, 'description' => 'Salad hoa quả tươi trộn sốt đặc biệt.'],
                ['name' => 'Tokbokki phô mai', 'category' => 'appetizer', 'price' => 79000, 'description' => 'Bánh gạo Hàn Quốc sốt cay phủ phô mai béo ngậy.'],
                ['name' => 'Combo thịt nướng Xèo Xèo Premium', 'category' => 'main', 'price' => 399000, 'description' => 'Buffet nướng trọn vẹn với các phần bò Mỹ thượng hạng, dẻ sườn và heo Iberico.'],
                ['name' => 'Cơm trộn đá nóng', 'category' => 'main', 'price' => 99000, 'description' => 'Cơm trộn Hàn Quốc đầy đủ thịt bò, rau củ và trứng lòng đào đựng trong tô đá nóng.'],
                ['name' => 'Bánh mochi kem', 'category' => 'dessert', 'price' => 35000, 'description' => 'Bánh mochi nhân kem mát lạnh ngọt dịu.'],
                ['name' => 'Nước mơ má đào Hàn Quốc', 'category' => 'drink', 'price' => 39000, 'description' => 'Nước quả mơ truyền thống lên men nhẹ thơm dịu.'],
                ['name' => 'Trà gạo rang đá', 'category' => 'drink', 'price' => 25000, 'description' => 'Trà gạo lứt rang thơm ngậy thanh lọc cơ thể.'],
            ],
            'Manwah Taiwanese Hotpot' => [
                ['name' => 'Há cảo tôm ngọc bích', 'category' => 'appetizer', 'price' => 69000, 'description' => 'Há cảo nhân tôm tươi ngon hấp chín nóng hổi.'],
                ['name' => 'Sủi cảo Đài Loan', 'category' => 'appetizer', 'price' => 59000, 'description' => 'Sủi cảo nhân thịt băm rau củ đậm đà.'],
                ['name' => 'Buffet Lẩu Đài Loan đặc sắc (Người lớn)', 'category' => 'main', 'price' => 329000, 'description' => 'Buffet lẩu tự chọn với nước lẩu Mala cay nồng hoặc lẩu nấm thanh ngọt kèm thịt bò Wagyu nhúng.'],
                ['name' => 'Kem tươi tự chọn Manwah', 'category' => 'dessert', 'price' => 39000, 'description' => 'Quầy tráng miệng kem tươi không giới hạn với nhiều hương vị.'],
                ['name' => 'Trà sữa Đài Loan trân châu', 'category' => 'drink', 'price' => 45000, 'description' => 'Trà sữa Đài Loan chuẩn vị béo ngậy kèm trân châu đen dai giòn.'],
            ],
            'Sushi Kei' => [
                ['name' => 'Súp miso rong biển', 'category' => 'appetizer', 'price' => 25000, 'description' => 'Súp đậu tương truyền thống Nhật Bản nấu cùng rong biển và đậu hũ.'],
                ['name' => 'Sashimi Cá hồi tươi (5 lát)', 'category' => 'appetizer', 'price' => 129000, 'description' => 'Cá hồi Na-uy tươi sống thái lát mỏng ăn kèm mù tạt và nước tương.'],
                ['name' => 'Set Sushi Kei Đặc biệt', 'category' => 'main', 'price' => 289000, 'description' => 'Set tổng hợp các loại sushi cuộn cơm lươn, sushi tôm, cá hồi và trứng cá hồi cao cấp.'],
                ['name' => 'Mỳ Udon bò Mỹ', 'category' => 'main', 'price' => 119000, 'description' => 'Mỳ Udon sợi to nấu nước dùng Dashi thanh ngọt ăn kèm thịt bò Mỹ.'],
                ['name' => 'Mochi trà xanh', 'category' => 'dessert', 'price' => 29000, 'description' => 'Bánh mochi truyền thống Nhật Bản phủ bột trà xanh.'],
                ['name' => 'Trà xanh Nhật Bản nóng/đá', 'category' => 'drink', 'price' => 15000, 'description' => 'Trà xanh Matcha pha theo phong cách Nhật.'],
            ],
            'ThaiExpress' => [
                ['name' => 'Gỏi đu đủ Som Tum', 'category' => 'appetizer', 'price' => 69000, 'description' => 'Gỏi đu đủ bào sợi vị chua cay mặn ngọt đặc trưng ẩm thực Thái.'],
                ['name' => 'Chả tôm chiên giòn', 'category' => 'appetizer', 'price' => 89000, 'description' => 'Thịt tôm băm tẩm bột chiên rụm ăn cùng sốt xí muội.'],
                ['name' => 'Nồi Lẩu Thái Tom Yum Hải sản', 'category' => 'main', 'price' => 359000, 'description' => 'Lẩu canh chua hải sản Tom Yum đậm đà nước cốt dừa, sả và lá chanh Thái.'],
                ['name' => 'Mỳ Phở xào Pad Thai tôm', 'category' => 'main', 'price' => 109000, 'description' => 'Phở xào kiểu Thái xào cùng tôm tươi, giá hẹ và đậu hũ sốt me.'],
                ['name' => 'Xôi xoài nước cốt dừa', 'category' => 'dessert', 'price' => 59000, 'description' => 'Xoài chín ngọt lịm ăn kèm xôi nếp dẻo thơm rưới nước cốt dừa béo ngậy.'],
                ['name' => 'Trà sữa Thái đỏ đá', 'category' => 'drink', 'price' => 35000, 'description' => 'Trà Thái đỏ pha sữa đặc ngọt ngào mát lạnh.'],
            ],
            'Le Monde Steak' => [
                ['name' => 'Súp kem bí đỏ', 'category' => 'appetizer', 'price' => 39000, 'description' => 'Súp bí đỏ sánh mịn ăn kèm bánh mỳ nướng bơ tỏi.'],
                ['name' => 'Bít tết Lõi vai bò Mỹ (Top Blade Steak 200g)', 'category' => 'main', 'price' => 219000, 'description' => 'Bít tết phần lõi vai mềm ngọt, rưới sốt tiêu xanh hoặc sốt bơ Pháp ăn kèm khoai tây chiên.'],
                ['name' => 'Mỳ Ý sốt kem nấm gà', 'category' => 'main', 'price' => 129000, 'description' => 'Mỳ Ý xào gà nấm rơm rưới sốt kem trắng sánh mịn béo ngậy.'],
                ['name' => 'Bánh Flan caramel kiểu Pháp', 'category' => 'dessert', 'price' => 35000, 'description' => 'Bánh flan mềm mịn thơm ngậy mùi trứng sữa rưới sốt đường cháy.'],
                ['name' => 'Rượu vang đỏ Pháp (Ly)', 'category' => 'drink', 'price' => 85000, 'description' => 'Ly rượu vang nhập khẩu kết hợp hoàn hảo cùng bít tết.'],
            ]
        ];

        // Seed món ăn cho từng nhà hàng
        foreach ($restaurants as $restaurant) {
            // Lấy tên nhà hàng để đối chiếu menu mẫu, mặc định nếu không có tên khớp thì lấy menu của Le Monde Steak
            $menuItems = $menus[$restaurant->name] ?? $menus['Le Monde Steak'];

            foreach ($menuItems as $item) {
                RestaurantMenuItem::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $item['name'],
                    ],
                    [
                        'category' => $item['category'],
                        'price' => $item['price'],
                        'description' => $item['description'],
                        'is_available' => true,
                    ]
                );
            }
        }

        $this->command->info('Đã seed thành công danh sách Món ăn thực đơn (Menu Items) cho toàn bộ Nhà hàng!');
    }
}
