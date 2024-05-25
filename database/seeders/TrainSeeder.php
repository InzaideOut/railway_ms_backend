<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Train;

class TrainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Train::create(['name' => 'Express 1', 'capacity' => 100]);
        Train::create(['name' => 'Express 2', 'capacity' => 150]);
    }
}
