<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        $branches = \App\Models\Branch::all();
        $movies = \App\Models\Movie::all();

        foreach ($branches as $branch) {
            foreach ($movies as $movie) {
                \App\Models\Showtime::create([
                    'movie_id' => $movie->id,
                    'branch_id' => $branch->id,
                    'start_time' => now()->addDays(rand(1, 7))->setTime(rand(10, 22), 0),
                    'price' => 50000,
                ]);
            }
        }
    }
}
