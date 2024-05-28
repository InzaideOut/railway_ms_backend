<?php

namespace Database\Seeders;

use App\Models\path;
use App\Models\route;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //     $routes = \App\Models\Route::all();

    // foreach ($routes as $route) {
    //     // Generate paths for each route
    //     // For simplicity, we'll just use some sample cities
    //     $cities = ['Lagos', 'Abuja', 'Kano', 'Port Harcourt', 'Kaduna', 'Enugu', 'Jos'];
    //     shuffle($cities);
        
    //     $departureCity = array_shift($cities);
    //     foreach ($cities as $arrivalCity) {
    //         \App\Models\Path::create([
    //             'route_id' => $route->id,
    //             'departure_city' => $departureCity,
    //             'arrival_city' => $arrivalCity,
    //         ]);
    //         $departureCity = $arrivalCity;
    //     }
    // }


        $routesPaths = [
            'Lagos to Oyo' => [
                ['Lagos', 'Abeokuta'],
                ['Abeokuta', 'Ibadan']
            ],
            'Lagos to Kano' => [
                ['Lagos', 'Abeokuta'],
                ['Abeokuta', 'Ibadan'],
                ['Ibadan', 'Ilorin'],
                ['Ilorin', 'Jebba'],
                ['Jebba', 'Minna'],
                ['Minna', 'Kaduna'],
                ['Kaduna', 'Zaria'],
                ['Zaria', 'Kano']
            ],
            'Abuja to Enugu' => [
                ['Abuja', 'Keffi'],
                ['Keffi', 'Akwanga'],
                ['Akwanga', 'Lafia'],
                ['Lafia', 'Makurdi'],
                ['Makurdi', 'Otukpo'],
                ['Otukpo', 'Enugu']
            ],
            'Kano to Jos' => [
                ['Kano', 'Zaria'],
                ['Zaria', 'Kaduna'],
                ['Kaduna', 'Jos']
            ],
        ];

        foreach ($routesPaths as $routeName => $cities) {
            $route = route::where('name', $routeName)->first();

            foreach ($cities as $path) {
                path::create([
                    'route_id' => $route->id,
                    'departure_city' => $path[0],
                    'arrival_city' => $path[1],
                ]);
            }
        }
    }
    }

