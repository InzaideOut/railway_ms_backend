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
            ['name' => 'Lagos to Oyo'],
            ['name' => 'Lagos to Kano'],
            ['name' => 'Abuja to Enugu'],
            ['name' => 'Kano to Jos'],
        ]);
    }
}
