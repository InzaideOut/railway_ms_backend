<?php

namespace App\Http\Controllers\Api;

use App\Models\Route;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserBookingController extends Controller
{

    public function listRoutes()
    {
        $routes = Route::all();
        return response()->json($routes);
    }

    public function getPaths($routeId)
    {
        $route = Route::findOrFail($routeId);
        $paths = explode(', ', $route->paths);
        return response()->json($paths);
    }

    private function calculateTime($index)
    {
        // Assuming each segment takes 1 hour
        // You can adjust this based on your actual data
        $travelTimePerSegment = 1; // in hours

        // Calculate total travel time from the beginning to the given index
        $totalTravelTime = $index * $travelTimePerSegment;

        // Add travel time to a starting time (e.g., 08:00)
        // Adjust this based on your actual starting time
        $startingTime = strtotime('08:00'); // Convert to UNIX timestamp
        $arrivalTime = date('H:i', $startingTime + $totalTravelTime * 3600); // Convert back to time format

        return $arrivalTime;
    }

    public function calculateDepartureAndArrivalTime(Request $request)
    {
        $routeId = $request->input('route_id');
        $departureCity = $request->input('departure_city');
        $arrivalCity = $request->input('arrival_city');
        $travelDate = $request->input('travel_date');

        // Retrieve route details
        $route = Route::findOrFail($routeId);

        // Get paths associated with the route
        $paths = explode(', ', $route->paths);

        // Validate user-selected departure and arrival cities
        if (!in_array($departureCity, $paths) || !in_array($arrivalCity, $paths)) {
            return response()->json(['error' => 'Invalid departure or arrival city selection for the chosen route.'], 400);
        }

        // Find positions of departure and arrival cities in the path sequence
        $departureIndex = array_search($departureCity, $paths);
        $arrivalIndex = array_search($arrivalCity, $paths);

        // Calculate departure time
        $departureTime = $this->calculateTime($departureIndex);

        // Calculate arrival time
        $arrivalTime = $this->calculateTime($arrivalIndex);

        // Format departure and arrival times with a hyphen (-)
        $formattedTime = $departureTime . ' - ' . $arrivalTime;

        return response()->json(['departure_and_arrival_time' => $formattedTime]);
    }

    

    public function bookTrain(Request $request)
    {
        // Validate the request data
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'departure_city' => 'required|string',
            'arrival_city' => 'required|string',
            'travel_date' => 'required|date',
            'departure_time' => 'required|string', // Assuming the user selects the departure time
            // You may include additional validation rules as needed
        ]);

        // Retrieve the validated data from the request
        $routeId = $request->input('route_id');
        $departureCity = $request->input('departure_city');
        $arrivalCity = $request->input('arrival_city');
        $travelDate = $request->input('travel_date');
        $departureTime = $request->input('departure_time');

        // Generate a seat number (You can implement your own logic here)
        $seatNumber = $this->generateSeatNumber();

        // Save the booking details to the database
        $booking = new Booking();
        $booking->route_id = $routeId;
        $booking->departure_city = $departureCity;
        $booking->arrival_city = $arrivalCity;
        $booking->travel_date = $travelDate;
        $booking->departure_time = $departureTime;
        $booking->seat_number = $seatNumber;
        $booking->status = 'confirmed'; // Assuming the booking status is confirmed
        $booking->save();

        // Return the booking confirmation to the user
        return response()->json([
            'message' => 'Booking confirmed!',
            'booking_details' => $booking,
        ]);
    }

    private function generateSeatNumber()
    {
        // You can implement your own logic to generate a seat number here
        // For example, you can assign seats sequentially or randomly
        // For simplicity, let's assume seats are assigned sequentially
        $lastBooking = Booking::latest()->first();
        $lastSeatNumber = $lastBooking ? $lastBooking->seat_number : 0;
        return ++$lastSeatNumber;
    }

}
