<?php

namespace App\Http\Controllers\Api;

use App\Models\booking;

use App\Models\route;
use App\Models\train;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class bookingController extends Controller
{
    public function index()
    {
        $bookings = booking::all();
        return response()->json($bookings);
    }

    public function show($id)
    {
        $booking = booking::findOrFail($id);
        return response()->json($booking);
    }

    public function destroy($id)
    {
        $booking = booking::findOrFail($id);
        $booking->delete();
        return response()->json(['message' => 'booking canceled successfully']);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'departure_city' => 'required|string',
            'arrival_city' => 'required|string',
            'date_time' => 'required|date',
        ]);

        $routes = route::withCities($validated['departure_city'], $validated['arrival_city'])->get();

        if ($routes->isEmpty()) {
            return response()->json(['error' => 'No routes available for the selected cities'], 400);
        }

        $trainFound = null;
        $boardingPoint = null;

        foreach ($routes as $route) {
            foreach ($route->trains as $train) {
                $stops = $train->boardingPoints->pluck('city')->toArray();
                if (in_array($validated['departure_city'], $stops) && in_array($validated['arrival_city'], $stops)) {
                    $trainFound = $train;
                    $boardingPoint = $train->boardingPoints->firstWhere('city', $validated['departure_city']);
                    break 2;
                }
            }
        }

        if (!$trainFound || !$boardingPoint) {
            return response()->json(['error' => 'No train available for the selected cities'], 400);
        }

        $booking = booking::create([
            'user_id' => $validated['user_id'],
            'train_id' => $trainFound->id,
            'route_id' => $route->id,
            'boarding_point_id' => $boardingPoint->id,
            'date_time' => $validated['date_time'],
            'status' => 'confirmed',
        ]);

        return response()->json($booking, 201);
    }


}
