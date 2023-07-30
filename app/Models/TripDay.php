<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDay extends Model
{
    use HasFactory;

    protected $fillables = [
        'trip_id',
        'day_number',
        'title',
        'details',
    ];

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

}
