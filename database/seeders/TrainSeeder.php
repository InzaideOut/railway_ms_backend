<?php

namespace Database\Seeders;

use App\Models\route;
use App\Models\Train;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TrainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('trains')->insert([
            ['name' => 'Express Train 1', 'type' => 'Express', 'route_id' => 1,  'capacity' => '5','initial_departure_time' => '08:00'],
            ['name' => 'Local Train 1', 'type' => 'Local', 'route_id' => 1, 'capacity' => '10','initial_departure_time' => '09:00'],
            ['name' => 'Express Train 2', 'type' => 'Express', 'route_id' => 2, 'capacity' => '50','initial_departure_time' => '07:30'],
            ['name' => 'Local Train 2', 'type' => 'Local', 'route_id' => 2, 'capacity' => '50','initial_departure_time' => '10:00'],
            // Add more sample trains as needed
        ]);
    }

        
}
