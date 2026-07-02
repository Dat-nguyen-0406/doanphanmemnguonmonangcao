<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $branches = \App\Models\Branch::all();

        foreach ($branches as $branch) {
            for ($row = 1; $row <= 5; $row++) {
                for ($seat = 1; $seat <= 10; $seat++) {
                    \App\Models\Seat::create([
                        'branch_id' => $branch->id,
                        'row' => chr(64 + $row), // A, B, C, D, E
                        'seat_number' => $seat,
                        'type' => $row <= 2 ? 'vip' : 'normal',
                    ]);
                }
            }
        }
    }
}
