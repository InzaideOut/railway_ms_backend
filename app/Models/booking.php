<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'train_id', 'route_id', 'boarding_point_id', 'date_time', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function train()
    {
        return $this->belongsTo(Train::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function boardingPoint()
    {
        return $this->belongsTo(BoardingPoint::class);
    }
}
