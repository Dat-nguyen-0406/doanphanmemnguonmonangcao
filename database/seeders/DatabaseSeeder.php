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
    $hanoi = \App\Models\City::create(['name' => 'Hà Nội', 'slug' => 'ha-noi']);
    
    $hanoi->branches()->create([
        'name' => 'AEON Mall Long Biên',
        'address' => 'Số 27 đường Cổ Linh, P. Long Biên, Q. Long Biên, Hà Nội',
        'map_link' => 'https://goo.gl/maps/...'
    ]);

    $hanoi->branches()->create([
        'name' => 'AEON Mall Hà Đông',
        'address' => 'Phường Dương Nội, Quận Hà Đông, Hà Nội',
    ]);
}
}
