<?php

namespace Database\Seeders;

// use App\Models\City;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'datznxt@gmail.com'], // Kiểm tra nếu email này chưa có thì mới tạo
            [
                'name' => 'Nguyễn Thành Đạt (Admin)',
                'password' => Hash::make('12345678'), // Đặt mật khẩu mặc định
                'phone' => '0123456789',
                'address' => 'Nam Định',
                'role' => 1, // QUAN TRỌNG: Gán quyền Admin (role = 1)
            ]
        );

        User::updateOrCreate(
            ['email' => 'abc@gmail.com'], // Kiểm tra nếu email này chưa có thì mới tạo
            [
                'name' => 'User Thường', // Tên người dùng bình thường
                'password' => Hash::make('12345678'), // Đặt mật khẩu mặc định
                'phone' => '0123456788',
                'address' => 'Cà Mau',
                'role' => 0, // QUAN TRỌNG: Gán quyền User bình thường (role = 0)
            ]
        );

        $hanoi = \App\Models\City::firstOrCreate(
            ['slug' => 'ha-noi'],
            ['name' => 'Hà Nội']
        );

        \App\Models\Branch::updateOrCreate(
            ['name' => 'AEON Mall Long Biên', 'city_id' => $hanoi->id],
            [
                'address' => 'Số 27 đường Cổ Linh, P. Long Biên, Q. Long Biên, Hà Nội',
                'map_link' => 'https://goo.gl/maps/...'
            ]
        );

        \App\Models\Branch::updateOrCreate(
            ['name' => 'AEON Mall Hà Đông', 'city_id' => $hanoi->id],
            [
                'address' => 'Phường Dương Nội, Quận Hà Đông, Hà Nội',
            ]
        );

        $this->call([
            RestaurantSeeder::class,
        ]);

        $this->call([
            RestaurantTableSeeder::class,
        ]);
    }
}
