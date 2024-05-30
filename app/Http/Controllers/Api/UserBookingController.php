<?php

namespace App\Http\Controllers\Api;

use App\Models\Path;
use App\Models\User;
use App\Models\Route;
use App\Models\Trains;
use App\Models\Booking;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserBookingController extends Controller
{

    // List all routes
    public function listRoutes()
    {
        $routes = Route::all();
        return response()->json($routes);
    }

    // Get paths associated with a route
    public function getPaths($routeId)
    {
        $paths = Path::where('route_id', $routeId)->orderBy('sequence')->get();
        return response()->json($paths);
    }

    // Helper function to calculate time
    private function calculateTime($index, $initialDepartureTime)
    {
        $travelTimePerSegment = 1; // in hours
        $totalTravelTime = ($index - 1) * $travelTimePerSegment;
        $startingTime = strtotime($initialDepartureTime);
        $calculatedTime = date('H:i', $startingTime + $totalTravelTime * 3600);

        return $calculatedTime;
    }

    // Common logic for calculating departure and arrival times and checking seat availability
    private function getDepartureAndArrivalDetails($routeId, $departureCityId, $arrivalCityId, $travelDate)
    {
        $route = Route::findOrFail($routeId);
        $trains = $route->trains;

        if ($trains->isEmpty()) {
            throw new \Exception('No trains available for the selected route.');
        }

        $departurePath = Path::where('route_id', $routeId)->where('id', $departureCityId)->first();
        $arrivalPath = Path::where('route_id', $routeId)->where('id', $arrivalCityId)->first();

        if (!$departurePath || !$arrivalPath) {
            throw new \Exception('Invalid departure or arrival city selection for the chosen route.');
        }

        if ($departurePath->sequence >= $arrivalPath->sequence) {
            throw new \Exception('Departure city must precede arrival city.');
        }

        foreach ($trains as $train) {
            $existingBookings = Booking::where('train_id', $train->id)
                ->where('travel_date', $travelDate)
                ->count();

            if ($existingBookings < $train->capacity) {
                $departureTime = $this->calculateTime($departurePath->sequence, $train->initial_departure_time);
                $arrivalTime = $this->calculateTime($arrivalPath->sequence, $train->initial_departure_time);

                return [
                    'route_name' => $route->name,
                    'train' => $train,
                    'departure_city' => $departurePath->city,
                    'arrival_city' => $arrivalPath->city,
                    'departure_time' => $departureTime,
                    'arrival_time' => $arrivalTime
                ];
            }
        }

        throw new \Exception('No seats available on any trains for the selected route on the given travel date.');
    }

    // Calculate departure and arrival times
    public function calculateDepartureAndArrivalTime(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'departure_city_id' => 'required|exists:paths,id',
            'arrival_city_id' => 'required|exists:paths,id',
            'travel_date' => 'required|date',
        ]);

        try {
            $details = $this->getDepartureAndArrivalDetails(
                $request->input('route_id'),
                $request->input('departure_city_id'),
                $request->input('arrival_city_id'),
                $request->input('travel_date')
            );

            return response()->json([
                'departure_city' => $details['departure_city'],
                'arrival_city' => $details['arrival_city'],
                'departure_and_arrival_time' => $details['departure_time'] . ' - ' . $details['arrival_time']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Generate seat number
    private function generateSeatNumber($trainId, $travelDate)
    {
        $existingBookings = Booking::where('train_id', $trainId)
            ->where('travel_date', $travelDate)
            ->count();

        return 'S' . ($existingBookings + 1);
    }

    // Generate ticket number
    private function generateTicketNumber()
    {
        return strtoupper(Str::random(8));
    }

    // Book a train
    public function bookTrain(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'route_id' => 'required|exists:routes,id',
            'departure_city_id' => 'required|exists:paths,id',
            'arrival_city_id' => 'required|exists:paths,id',
            'travel_date' => 'required|date',
        ]);

        try {
            $details = $this->getDepartureAndArrivalDetails(
                $request->input('route_id'),
                $request->input('departure_city_id'),
                $request->input('arrival_city_id'),
                $request->input('travel_date')
            );

            $seatNumber = $this->generateSeatNumber($details['train']->id, $request->input('travel_date'));
            $ticketNumber = $this->generateTicketNumber();

            $booking = Booking::create([
                'user_id' => $request->input('user_id'),
                'train_id' => $details['train']->id,
                'departure_city' => $details['departure_city'],
                'arrival_city' => $details['arrival_city'],
                'travel_date' => $request->input('travel_date'),
                'departure_time' => $details['departure_time'],
                'arrival_time' => $details['arrival_time'],
                'seat_number' => $seatNumber,
                'ticket_number' => $ticketNumber,
            ]);

            $user = User::findOrFail($request->input('user_id'));

            return response()->json([
                'user' => [
                    'first_name' => $user->firstname,
                    'last_name' => $user->lastname,
                ],
                'route_name' => $details['route_name'],
                'train' => $details['train'],
                'departure_city' => $details['departure_city'],
                'arrival_city' => $details['arrival_city'],
                'travel_date' => $request->input('travel_date'),
                'departure_time' => $details['departure_time'],
                'arrival_time' => $details['arrival_time'],
                'seat_number' => $seatNumber,
                'ticket_number' => $ticketNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function checkBooking(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string|size:8',
        ]);

        $booking = Booking::where('ticket_number', $request->input('ticket_number'))->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }

        $user = User::findOrFail($booking->user_id);
        $train = Trains::findOrFail($booking->train_id);
        $route = Route::findOrFail($train->route_id);

        return response()->json([
            'user' => [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
            ],
            'route_name' => $route->name,
            'train' => $train,
            'departure_city' => $booking->departure_city,
            'arrival_city' => $booking->arrival_city,
            'travel_date' => $booking->travel_date,
            'departure_time' => $booking->departure_time,
            'arrival_time' => $booking->arrival_time,
            'seat_number' => $booking->seat_number,
            'ticket_number' => $booking->ticket_number,
        ]);
    }


    //need to restructure the code due o some reasons
        // List all routes
        // public function listRoutes()
        // {
        //     $routes = Route::all();
        //     return response()->json($routes);
        // }
    
        // // Get paths associated with a route
        // public function getPaths($routeId)
        // {
        //     $paths = Path::where('route_id', $routeId)->orderBy('sequence')->get();
        //     return response()->json($paths);
        // }
    
        // // Helper function to calculate time
        // private function calculateTime($index, $initialDepartureTime)
        // {
        //     $travelTimePerSegment = 1; // in hours
        //     $totalTravelTime = ($index - 1) * $travelTimePerSegment;
        //     $startingTime = strtotime($initialDepartureTime);
        //     $calculatedTime = date('H:i', $startingTime + $totalTravelTime * 3600);
    
        //     return $calculatedTime;
        // }
    
        // // Common logic for calculating departure and arrival times
        // private function getDepartureAndArrivalDetails($routeId, $departureCityId, $arrivalCityId)
        // {
        //     $route = Route::findOrFail($routeId);
        //     $train = $route->trains()->first();
            
        //     if (!$train) {
        //         throw new \Exception('No trains available for the selected route.');
        //     }
    
        //     $departurePath = Path::where('route_id', $routeId)->where('id', $departureCityId)->first();
        //     $arrivalPath = Path::where('route_id', $routeId)->where('id', $arrivalCityId)->first();
    
        //     if (!$departurePath || !$arrivalPath) {
        //         throw new \Exception('Invalid departure or arrival city selection for the chosen route.');
        //     }
    
        //     if ($departurePath->sequence >= $arrivalPath->sequence) {
        //         throw new \Exception('Departure city must precede arrival city.');
        //     }
    
        //     $departureTime = $this->calculateTime($departurePath->sequence, $train->initial_departure_time);
        //     $arrivalTime = $this->calculateTime($arrivalPath->sequence, $train->initial_departure_time);
    
        //     return [
        //         'route_name' => $route->name,
        //         'train' => $train,
        //         'departure_city' => $departurePath->city,
        //         'arrival_city' => $arrivalPath->city,
        //         'departure_time' => $departureTime,
        //         'arrival_time' => $arrivalTime
        //     ];
        // }
    
        // // Calculate departure and arrival times
        // public function calculateDepartureAndArrivalTime(Request $request)
        // {
        //     try {
        //         $details = $this->getDepartureAndArrivalDetails(
        //             $request->input('route_id'),
        //             $request->input('departure_city_id'),
        //             $request->input('arrival_city_id')
        //         );
    
        //         return response()->json([
        //             'departure_city' => $details['departure_city'],
        //             'arrival_city' => $details['arrival_city'],
        //             'departure_and_arrival_time' => $details['departure_time'] . ' - ' . $details['arrival_time']
        //         ]);
        //     } catch (\Exception $e) {
        //         return response()->json(['error' => $e->getMessage()], 400);
        //     }
        // }
    
        // // Generate seat number
        // private function generateSeatNumber()
        // {
        //     return 'S' . rand(1, 100);
        // }
    
        // // Generate ticket number
        // private function generateTicketNumber()
        // {
        //     return strtoupper(Str::random(8));
        // }
    
        // // Book a train
        // public function bookTrain(Request $request)
        // {
        //     $request->validate([
        //         'user_id' => 'required|exists:users,id',
        //         'route_id' => 'required|exists:routes,id',
        //         'departure_city_id' => 'required|exists:paths,id',
        //         'arrival_city_id' => 'required|exists:paths,id',
        //         'travel_date' => 'required|date',
        //     ]);
    
        //     try {
        //         $details = $this->getDepartureAndArrivalDetails(
        //             $request->input('route_id'),
        //             $request->input('departure_city_id'),
        //             $request->input('arrival_city_id')
        //         );
    
        //         $seatNumber = $this->generateSeatNumber();
        //         $ticketNumber = $this->generateTicketNumber();
    
        //         $booking = Booking::create([
        //             'user_id' => $request->input('user_id'),
        //             'route_id' => $request->input('route_id'),
        //             'departure_city' => $details['departure_city'],
        //             'arrival_city' => $details['arrival_city'],
        //             'travel_date' => $request->input('travel_date'),
        //             'departure_time' => $details['departure_time'],
        //             'arrival_time' => $details['arrival_time'],
        //             'seat_number' => $seatNumber,
        //             'ticket_number' => $ticketNumber,
        //         ]);
    
        //         $user = User::findOrFail($request->input('user_id'));
    
        //         return response()->json([
        //             'user' => [
        //                 'first_name' => $user->first_name,
        //                 'last_name' => $user->last_name,
        //             ],
        //             'route_name' => $details['route_name'],
        //             'departure_city' => $details['departure_city'],
        //             'arrival_city' => $details['arrival_city'],
        //             'travel_date' => $request->input('travel_date'),
        //             'departure_time' => $details['departure_time'],
        //             'arrival_time' => $details['arrival_time'],
        //             'seat_number' => $seatNumber,
        //             'ticket_number' => $ticketNumber,
        //         ]);
        //     } catch (\Exception $e) {
        //         return response()->json(['error' => $e->getMessage()], 400);
        //     }
        // }


    

















































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

    

    // public function bookTrain(Request $request)
    // {
    //     // Validate the request data
    //     $request->validate([
    //         'route_id' => 'required|exists:routes,id',
    //         'departure_city' => 'required|string',
    //         'arrival_city' => 'required|string',
    //         'travel_date' => 'required|date',
    //         'departure_time' => 'required|string', // Assuming the user selects the departure time
    //         // You may include additional validation rules as needed
    //     ]);

    //     // Retrieve the validated data from the request
    //     $routeId = $request->input('route_id');
    //     $departureCity = $request->input('departure_city');
    //     $arrivalCity = $request->input('arrival_city');
    //     $travelDate = $request->input('travel_date');
    //     $departureTime = $request->input('departure_time');

    //     // Generate a seat number (You can implement your own logic here)
    //     $seatNumber = $this->generateSeatNumber();

    //     // Save the booking details to the database
    //     $booking = new Booking();
    //     $booking->route_id = $routeId;
    //     $booking->departure_city = $departureCity;
    //     $booking->arrival_city = $arrivalCity;
    //     $booking->travel_date = $travelDate;
    //     $booking->departure_time = $departureTime;
    //     $booking->seat_number = $seatNumber;
    //     $booking->status = 'confirmed'; // Assuming the booking status is confirmed
    //     $booking->save();

    //     // Return the booking confirmation to the user
    //     return response()->json([
    //         'message' => 'Booking confirmed!',
    //         'booking_details' => $booking,
    //     ]);
    // }

    // private function generateSeatNumber()
    // {
    //     // You can implement your own logic to generate a seat number here
    //     // For example, you can assign seats sequentially or randomly
    //     // For simplicity, let's assume seats are assigned sequentially
    //     $lastBooking = Booking::latest()->first();
    //     $lastSeatNumber = $lastBooking ? $lastBooking->seat_number : 0;
    //     return ++$lastSeatNumber;
    // }

}
