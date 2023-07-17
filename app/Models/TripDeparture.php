<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDeparture extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'flight_id',
        'departure_details'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function dates(){
        return $this->hasMany(TripDate::class,'departure_id');
    }

    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }

}
