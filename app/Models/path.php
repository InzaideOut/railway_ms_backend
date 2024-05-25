<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class path extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id', 'segment_number', 'departure_city', 'arrival_city',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function boardingPoints()
    {
        return $this->hasMany(BoardingPoint::class);
    }
}
