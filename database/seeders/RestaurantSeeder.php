<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tìm ID của AEON Mall (Tìm từ khóa 'Long Biên' hoặc 'Aeon' để tránh lỗi nếu dữ liệu seed Branches khác tên)
        $branch = Branch::where('name', 'LIKE', '%Long Biên%')
                        ->orWhere('name', 'LIKE', '%Aeon%')
                        ->first();

        // Nếu chưa có chi nhánh nào thì báo lỗi và dừng lại
        if (!$branch) {
            $this->command->error('Chưa có chi nhánh AEON nào trong database. Vui lòng thêm dữ liệu vào bảng branches trước!');
            return;
        }

        // Danh sách các nhà hàng thực tế tại AEON Mall Long Biên
        $restaurants = [
            [
                'name' => 'Kichi Kichi',
                'cuisine_type' => 'Lẩu băng chuyền',
                'description' => 'Chuỗi nhà hàng lẩu băng chuyền hàng đầu Việt Nam với đa dạng món nhúng phong cách Nhật Bản.',
                'image_url' => 'https://aeonmall-long-bien.com.vn/wp-content/uploads/2018/05/kichi-kichi.jpg',
            ],
            [
                'name' => 'GoGi House',
                'cuisine_type' => 'Thịt nướng Hàn Quốc',
                'description' => 'Quán thịt nướng Hàn Quốc ngon số 1, đưa bạn đến các quán thịt nướng tại Seoul với thịt nướng đậm đà, tươi ngon.',
                'image_url' => 'https://aeonmall-long-bien.com.vn/wp-content/uploads/2018/05/gogi-house.jpg',
            ],
            [
                'name' => 'Manwah Taiwanese Hotpot',
                'cuisine_type' => 'Lẩu Đài Loan',
                'description' => 'Trải nghiệm lẩu truyền thống Đài Loan với nước cốt lẩu ngọt thanh và thịt bò hảo hạng nhúng kèm.',
                'image_url' => 'https://aeonmall-long-bien.com.vn/wp-content/uploads/2019/12/manwah.jpg',
            ],
            [
                'name' => 'Sushi Kei',
                'cuisine_type' => 'Món Nhật Bản',
                'description' => 'Trải nghiệm ẩm thực Nhật Bản đích thực với sushi, sashimi tươi ngon được chế biến bởi các đầu bếp chuyên nghiệp.',
                'image_url' => 'https://aeonmall-long-bien.com.vn/wp-content/uploads/2018/05/sushi-kei.jpg',
            ],
            [
                'name' => 'ThaiExpress',
                'cuisine_type' => 'Món Thái Lan',
                'description' => 'Khám phá hương vị ẩm thực đường phố Thái Lan chân thực, kết hợp hoàn hảo giữa các vị chua, cay, mặn, ngọt.',
                'image_url' => 'https://aeonmall-long-bien.com.vn/wp-content/uploads/2018/05/thai-express.jpg',
            ],
            [
                'name' => 'Le Monde Steak',
                'cuisine_type' => 'Bít tết kiểu Pháp',
                'description' => 'Nhà hàng bít tết phong cách thành thị Pháp với mức giá tầm trung, không gian lãng mạn và tinh tế.',
                'image_url' => 'https://aeonmall-long-bien.com.vn/wp-content/uploads/2020/07/le-monde-steak.jpg',
            ]
        ];

        // Lặp qua mảng và thêm vào cơ sở dữ liệu
        foreach ($restaurants as $res) {
            // Dùng updateOrCreate để khi chạy lệnh seed nhiều lần sẽ không bị tạo trùng lặp
            Restaurant::updateOrCreate(
                [
                    'name' => $res['name'],
                    'branch_id' => $branch->id,
                ],
                [
                    'cuisine_type' => $res['cuisine_type'],
                    'description' => $res['description'],
                    'image_url' => $res['image_url'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Đã seed thành công các nhà hàng tại ' . $branch->name . '!');
    }
}