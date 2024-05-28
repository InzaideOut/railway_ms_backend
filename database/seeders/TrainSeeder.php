<?php

namespace Database\Seeders;

use App\Models\route;
use App\Models\Train;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TrainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $routes = route::all();
        $trainNames = [
            'Express Train 1',
            'Express Train 2',
            'Express Train 3',
            'Regional Train 1',
            'Regional Train 2',
            'Local Train 1',
            'Local Train 2',
            'Local Train 3'
        ];

        foreach ($routes as $route) {
            // Generate random number of trains for each route
            $trainCount = rand(1, 4);
            for ($i = 0; $i < $trainCount; $i++) {
                train::create([
                    'route_id' => $route->id,
                    'name' => $trainNames[array_rand($trainNames)],
                    'capacity' => rand(100, 300), // Random capacity between 100 and 300
                ]);
            }
        }
    }
    //     $routes = \App\Models\Route::all();

    //     foreach ($routes as $route) {
    //         // Generate random number of trains for each route
    //         $trainCount = rand(1, 4);
    //         for ($i = 0; $i < $trainCount; $i++) {
    //             train::create([
    //                 'route_id' => $route->id,
    //                 'capacity' => rand(100, 300), // Random capacity between 100 and 300
    //             ]);
    //         }
    //     }
    // }
}
