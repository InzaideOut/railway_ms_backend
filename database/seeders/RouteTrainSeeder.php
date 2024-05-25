<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Train;

class RouteTrainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $train1 = Train::find(1);
        $train2 = Train::find(2);
        
        $route1 = Route::find(1);
        $route2 = Route::find(2);
        
        // Attach trains to routes
        $route1->trains()->attach($train1);
        $route1->trains()->attach($train2);

        $route2->trains()->attach($train1);

    }
}
