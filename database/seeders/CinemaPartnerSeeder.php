<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CinemaPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo Cinema Partner cho AEON Mall Long Biên
        $aeonLongBien = Branch::where('name', 'AEON Mall Long Biên')->first();
        if ($aeonLongBien) {
            User::updateOrCreate(
                ['email' => 'partner.longbien@aeon.com'],
                [
                    'name' => 'Quản lý AEON Long Biên',
                    'password' => Hash::make('123456'),
                    'phone' => '0901234567',
                    'address' => 'AEON Mall Long Biên',
                    'role' => 2, // Cinema Partner
                    'branch_id' => $aeonLongBien->id,
                ]
            );
        }

        // Tạo Cinema Partner cho AEON Mall Hà Đông
        $aeonHaDong = Branch::where('name', 'AEON Mall Hà Đông')->first();
        if ($aeonHaDong) {
            User::updateOrCreate(
                ['email' => 'partner.hadong@aeon.com'],
                [
                    'name' => 'Quản lý AEON Hà Đông',
                    'password' => Hash::make('123456'),
                    'phone' => '0902234567',
                    'address' => 'AEON Mall Hà Đông',
                    'role' => 2, // Cinema Partner
                    'branch_id' => $aeonHaDong->id,
                ]
            );
        }
    }
}
