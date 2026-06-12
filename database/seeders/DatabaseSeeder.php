<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Tạo tài khoản Admin
        User::updateOrCreate(
            ['email' => 'datznxt@gmail.com'],
            [
                'name' => 'Nguyễn Thành Đạt (Admin)',
                'password' => Hash::make('12345678'),
                'phone' => '0123456789',
                'address' => 'Nam Định',
                'role' => 1,
            ]
        );
        User::updateOrCreate(
            ['email' => 'cinemal@gmail.com'],
            [
                'name' => 'phim',
                'password' => Hash::make('12345678'),
                'phone' => '0123456789',
                'address' => 'Nam Định',
                'role' => 2,
            ]
        );
        User::updateOrCreate(
            ['email' => 'food@gmail.com'],
            [
                'name' => 'food',
                'password' => Hash::make('12345678'),
                'phone' => '0123456789',
                'address' => 'Nam Định',
                'role' => 3,
            ]
        );
        User::updateOrCreate(
            ['email' => 'nike@gmail.com'],
            [
                'name' => 'nike',
                'password' => Hash::make('12345678'),
                'phone' => '0123456789',
                'address' => 'Nam Định',
                'role' => 4,
            ]
        );
        User::updateOrCreate(
            ['email' => 'quang@gmail.com'],
            [
                'name' => 'Quang',
                'password' => Hash::make('12345678'),
                'phone' => '0123456789',
                'address' => 'Nam Định',
                'role' => 0,
            ]
        );
    

        // Tạo Cities & Branches
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

        // Gọi seeders cho Cinema (từ doanphanmem)
        $this->call([
            MovieSeeder::class,
            ShowtimeSeeder::class,
            SeatSeeder::class,
            CinemaPartnerSeeder::class,
        ]);

        // Gọi seeders cho Shop (từ kethop) - nếu cần
        // $this->call([
        //     ProductSeeder::class,
        //     OrderSeeder::class,
        // ]);
    }
}
