<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('routes')->insert([
            ['name' => 'Lagos to Ibadan', 'paths' => 'Lagos, Abeokuta, Ibadan'],
            ['name' => 'Lagos to Kano', 'paths' => 'Lagos, Abeokuta, Ibadan, Ilorin, Jebba, Minna, Kaduna, Zaria, Kano'],
            ['name' => 'Lagos to Abuja', 'paths' => 'Lagos, Ibadan, Ilorin, Abuja'],
            ['name' => 'Kano to Abuja', 'paths' => 'Kano, Zaria, Kaduna, Abuja'],
            ['name' => 'Port Harcourt to Enugu', 'paths' => 'Port Harcourt, Aba, Umuahia, Enugu'],
            ['name' => 'Ibadan to Ilorin', 'paths' => 'Ibadan, Oshogbo, Ilorin'],
            ['name' => 'Jos to Maiduguri', 'paths' => 'Jos, Bauchi, Gombe, Damaturu, Maiduguri'],
            ['name' => 'Abuja to Calabar', 'paths' => 'Abuja, Lokoja, Okene, Akure, Benin, Uyo, Calabar'],
            // Add more sample routes as needed
        ]);
    }
}
