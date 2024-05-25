<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class route extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'name',
    // ];

    // public function paths()
    // {
    //     return $this->hasMany(Path::class);
    // }

    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class);
    // }

    // public function boardingPoints()
    // {
    //     return $this->hasMany(BoardingPoint::class);
    // }
    protected $fillable = ['name'];

    public function paths()
    {
        return $this->hasMany(Path::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function boardingPoints()
    {
        return $this->hasMany(BoardingPoint::class);
    }

    public function trains()
    {
        return $this->belongsToMany(Train::class, 'route_train');
    }

    public function scopeWithCities($query, $departureCity, $arrivalCity)
    {
        return $query->whereHas('paths', function ($q) use ($departureCity, $arrivalCity) {
            $q->where('departure_city', $departureCity)
              ->orWhere('arrival_city', $departureCity)
              ->orWhere('departure_city', $arrivalCity)
              ->orWhere('arrival_city', $arrivalCity);
        });
    }
}
