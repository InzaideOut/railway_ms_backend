<?php

namespace App\Http\Controllers\Api;

use App\Models\Path;
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

    //NEW METHOD
    //USING PATH->CITYID FOR DEPARTURE AND ARRIVAL ALSO USING INITIAL ARRIVAL TIME FOR TRAIN 

    private function calculateTime($index, $initialDepartureTime)
    {
        // Assuming each segment takes 1 hour
        $travelTimePerSegment = 1; // in hours

        // Calculate total travel time from the beginning to the given index
        $totalTravelTime = ($index - 1) * $travelTimePerSegment;

        // Add travel time to the initial departure time
        $startingTime = strtotime($initialDepartureTime); // Convert to UNIX timestamp
        $arrivalTime = date('H:i', $startingTime + $totalTravelTime * 3600); // Convert back to time format

        return $arrivalTime;
    }

    public function calculateDepartureAndArrivalTime(Request $request)
    {
        $routeId = $request->input('route_id');
        $departureCityId = $request->input('departure_city_id');
        $arrivalCityId = $request->input('arrival_city_id');
        $travelDate = $request->input('travel_date');

        // Retrieve route details
        $route = Route::findOrFail($routeId);

        // Debug: Check if trains are being fetched
        $train = $route->trains()->first();
        if (!$train) {
            return response()->json(['error' => 'No trains available for the selected route.'], 404);
        }


        // Get the initial departure time for the first train on the route
        $initialDepartureTime = $train->initial_departure_time;

        // Validate user-selected departure and arrival cities
        $departurePath = Path::where('route_id', $routeId)->where('id', $departureCityId)->first();
        $arrivalPath = Path::where('route_id', $routeId)->where('id', $arrivalCityId)->first();

        if (!$departurePath || !$arrivalPath) {
            return response()->json(['error' => 'Invalid departure or arrival city selection for the chosen route.'], 400);
        }

        // Find positions of departure and arrival cities in the path sequence
        $departureIndex = $departurePath->sequence;
        $arrivalIndex = $arrivalPath->sequence;


        // Ensure departure index is less than arrival index
        if ($departureIndex >= $arrivalIndex) {
            return response()->json(['error' => 'Departure city must precede arrival city.'], 400);
        }

        // Calculate departure time with initial departure time
        $departureTime = $this->calculateTime($departureIndex, $initialDepartureTime);

        // Calculate arrival time with initial departure time
        $arrivalTime = $this->calculateTime($arrivalIndex, $initialDepartureTime);

        // Format departure and arrival times with a hyphen (-)
        $formattedTime = $departureTime . ' - ' . $arrivalTime;

        return response()->json([
            'initial_departure_time' => $initialDepartureTime,
            'departure_city' => $departurePath->city,
            'arrival_city' => $arrivalPath->city,
            'departure_and_arrival_time' => $formattedTime,
            'train' => [
                // 'id' => $train->id,
                'name' => $train->name,
                'type' => $train->type,
                // 'route_id' => $train->route_id
            ],
            'route_name' => $route->name
            // 'initial_departure_time' => $initialDepartureTime,
            // 'departure_city' => $departurePath->city,
            // 'arrival_city' => $arrivalPath->city,
            // 'departure_and_arrival_time' => $formattedTime
        ]);

        // return response()->json(['departure_and_arrival_time' => $formattedTime]);
    }


















































    //WORKING FINE JUST WANTED TO TRY USING THE PATH ID FOR DEPARTURE AND ARRIVAL CITY 
    // private function calculateTime($index)
    // {
    //     // Assuming each segment takes 1 hour
    //     // You can adjust this based on your actual data
    //     $travelTimePerSegment = 1; // in hours

    //     // Calculate total travel time from the beginning to the given index
    //     $totalTravelTime = $index * $travelTimePerSegment;

    //     // Add travel time to a starting time (e.g., 08:00)
    //     // Adjust this based on your actual starting time
    //     $startingTime = strtotime('08:00'); // Convert to UNIX timestamp
    //     $arrivalTime = date('H:i', $startingTime + $totalTravelTime * 3600); // Convert back to time format

    //     return $arrivalTime;
    // }

    // public function calculateDepartureAndArrivalTime(Request $request)
    // {
    //     $routeId = $request->input('route_id');
    //     $departureCity = $request->input('departure_city');
    //     $arrivalCity = $request->input('arrival_city');
    //     $travelDate = $request->input('travel_date');

    //     // Retrieve route details
    //     $route = Route::findOrFail($routeId);

    //     // Get paths associated with the route
    //     $paths = explode(', ', $route->paths);

    //     // Validate user-selected departure and arrival cities
    //     if (!in_array($departureCity, $paths) || !in_array($arrivalCity, $paths)) {
    //         return response()->json(['error' => 'Invalid departure or arrival city selection for the chosen route.'], 400);
    //     }

    //     // Find positions of departure and arrival cities in the path sequence
    //     $departureIndex = array_search($departureCity, $paths);
    //     $arrivalIndex = array_search($arrivalCity, $paths);

    //     // Calculate departure time
    //     $departureTime = $this->calculateTime($departureIndex);

    //     // Calculate arrival time
    //     $arrivalTime = $this->calculateTime($arrivalIndex);

    //     // Format departure and arrival times with a hyphen (-)
    //     $formattedTime = $departureTime . ' - ' . $arrivalTime;

    //     return response()->json(['departure_and_arrival_time' => $formattedTime]);
    // }

    

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
