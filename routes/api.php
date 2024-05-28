<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PathController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\TrainController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\UserBookingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('user')->group(function () {
    Route::get('/searchtrain', [UserBookingController::class, 'search']);
    Route::get('/routes/{route_id}/paths', [UserBookingController::class, 'getPaths']);
    Route::get('/trains/search-by-route-and-date', [UserBookingController::class, 'searchByRouteAndDate']);
    Route::get('/trains/search-by-cities-and-date', [UserBookingController::class, 'searchByCitiesAndDate']);


});


Route::middleware(['auth:sanctum'])->group(function () {
    
    // Admin (ITCC) routes
    Route::prefix('admin')->group(function () {
        

       


    });

    

});


