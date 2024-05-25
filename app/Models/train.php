<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class train extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'capacity',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_train');
    }
}
