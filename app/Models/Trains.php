<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trains extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 
        'type',
        'route_id',
        'capacity',
        'initial_departure_time'
    ];


    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
