<?php

namespace App\Http\Controllers\Api;

use App\Models\route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserBookingController extends Controller
{
    public function search(Request $request)
    {
        $departureCity = $request->input('departure_city');
        $arrivalCity = $request->input('arrival_city');
        $travelDate = $request->input('travel_date');

        // Find routes with the specified departure and arrival cities
        $routes = route::whereHas('trains', function ($query) use ($departureCity, $arrivalCity, $travelDate) {
            $query->where('departure_city', $departureCity)
                  ->where('arrival_city', $arrivalCity);
        })->with(['trains' => function ($query) use ($travelDate) {
            $query->withCount(['bookings as available_seats' => function ($query) use ($travelDate) {
                $query->whereDate('travel_date', $travelDate);
            }]);
        }])->get();

        return response()->json($routes);
    }
}
