<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserBookingController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('/booking/routes', [UserBookingController::class, 'listRoutes']);
Route::get('/booking/routes/{routeId}/paths', [UserBookingController::class, 'getPaths']);
Route::post('/booking/calculate-time', [UserBookingController::class, 'calculateDepartureAndArrivalTime']);
Route::post('/booking/book-train', [UserBookingController::class, 'bookTrain']);
Route::middleware('auth:api')->get('/booking/user/bookings', [UserBookingController::class, 'userBookings']);




// Route::middleware(['auth:sanctum'])->group(function () {
    
//     // Admin (ITCC) routes
//     Route::prefix('admin')->group(function () {
        

       


//     });

    

// });



