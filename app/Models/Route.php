<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'paths'
    ];




    public function paths()
    {
        return $this->hasMany(Path::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
