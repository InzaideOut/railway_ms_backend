<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class boardingPoint extends Model
{
    use HasFactory;
    protected $fillable = [
        'route_id', 'path_id', 'city', 'station_name',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
