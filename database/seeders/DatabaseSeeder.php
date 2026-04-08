<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php
    public function run(): void
    {
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
