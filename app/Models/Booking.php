<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'user_id', 'train_id', 'route_id', 'boarding_point_id', 'date_time', 'status',
    // ];

    protected $fillable = [
        'user_id', 
        'train_id', 
        'departure_city', 
        'arrival_city', 
        'travel_date', 
        'departure_time', 
        'arrival_time', 
        'seat_number',
        'ticket_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
