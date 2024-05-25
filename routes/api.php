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

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Admin (ITCC) routes
    Route::prefix('admin')->group(function () {
        Route::get('routes', [RouteController::class, 'index']);
        Route::post('routes', [RouteController::class, 'store']);
        Route::get('routes/{id}', [RouteController::class, 'show']);
        Route::put('routes/{id}', [RouteController::class, 'update']);
        Route::delete('routes/{id}', [RouteController::class, 'destroy']);

        Route::get('routes/{routeId}/paths', [PathController::class, 'index']);
        Route::post('routes/{routeId}/paths', [PathController::class, 'store']);
        Route::get('routes/{routeId}/paths/{pathId}', [PathController::class, 'show']);
        Route::put('routes/{routeId}/paths/{pathId}', [PathController::class, 'update']);
        Route::delete('routes/{routeId}/paths/{pathId}', [PathController::class, 'destroy']);
    
        Route::get('trains', [TrainController::class, 'index']);
        Route::post('trains', [TrainController::class, 'store']);
        Route::get('trains/{id}', [TrainController::class, 'show']);
        Route::put('trains/{id}', [TrainController::class, 'update']);
        Route::delete('trains/{id}', [TrainController::class, 'destroy']);

        Route::get('bookings', [BookingController::class, 'index']);
        Route::get('bookings/{id}', [BookingController::class, 'show']);
        Route::delete('bookings/{id}', [BookingController::class, 'destroy']);


        Route::get('users', [UserController::class, 'index']);
        // Route::get('users/{id}', [UserController::class, 'show']);
        // Route::put('users/{id}', [UserController::class, 'update']);
        // Route::delete('users/{id}', [UserController::class, 'destroy']);
    


    });

    Route::prefix('user')->group(function () {
        Route::get('/searchtrain', [UserBookingController::class, 'search']);


    });

});


