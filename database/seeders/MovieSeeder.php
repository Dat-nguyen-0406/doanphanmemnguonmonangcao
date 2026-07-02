<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;

class MovieSeeder extends Seeder
{
    // database/seeders/MovieSeeder.php
public function run(): void
{
    \App\Models\Movie::updateOrCreate(
        ['title' => 'Avengers: Endgame'],
        [
            'description' => 'After the devastating events of Infinity War...',
            'duration' => 181,
            'release_date' => '2019-04-26',
            'genre' => 'Action, Adventure, Drama',
            'poster' => '/storage/movies/abc.jpg',
        ]
    );
}
}