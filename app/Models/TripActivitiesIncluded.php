<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripActivitiesIncluded extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'activity_id',
    ];


}
