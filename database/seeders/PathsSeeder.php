<?php

namespace Database\Seeders;

use App\Models\path;
use App\Models\route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

         // Paths for "Lagos to Ibadan" route
         DB::table('paths')->insert([
            ['route_id' => 1, 'city' => 'Lagos', 'sequence' => 1],
            ['route_id' => 1, 'city' => 'Abeokuta', 'sequence' => 2],
            ['route_id' => 1, 'city' => 'Ibadan', 'sequence' => 3],
        ]);

        // Paths for "Lagos to Kano" route
        DB::table('paths')->insert([
            ['route_id' => 2, 'city' => 'Lagos', 'sequence' => 1],
            ['route_id' => 2, 'city' => 'Abeokuta', 'sequence' => 2],
            ['route_id' => 2, 'city' => 'Ibadan', 'sequence' => 3],
            ['route_id' => 2, 'city' => 'Ilorin', 'sequence' => 4],
            ['route_id' => 2, 'city' => 'Jebba', 'sequence' => 5],
            ['route_id' => 2, 'city' => 'Minna', 'sequence' => 6],
            ['route_id' => 2, 'city' => 'Kaduna', 'sequence' => 7],
            ['route_id' => 2, 'city' => 'Zaria', 'sequence' => 8],
            ['route_id' => 2, 'city' => 'Kano', 'sequence' => 9],
        ]);

        // Paths for "Lagos to Abuja" route
        DB::table('paths')->insert([
            ['route_id' => 3, 'city' => 'Lagos', 'sequence' => 1],
            ['route_id' => 3, 'city' => 'Ibadan', 'sequence' => 2],
            ['route_id' => 3, 'city' => 'Ilorin', 'sequence' => 3],
            ['route_id' => 3, 'city' => 'Abuja', 'sequence' => 4],
        ]);

        // Paths for "Kano to Abuja" route
        DB::table('paths')->insert([
            ['route_id' => 4, 'city' => 'Kano', 'sequence' => 1],
            ['route_id' => 4, 'city' => 'Zaria', 'sequence' => 2],
            ['route_id' => 4, 'city' => 'Kaduna', 'sequence' => 3],
            ['route_id' => 4, 'city' => 'Abuja', 'sequence' => 4],
        ]);

        // Add paths for the remaining routes similarly
   
    
    }
}

