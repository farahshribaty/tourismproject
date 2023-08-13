<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class FlightsTime extends Model
{
    use HasFactory,HasApiTokens,Notifiable;

    protected $fillable = [
    'departe_day',
    'From_hour',
    'To_hour',
    'duration',
    'flights_id',
    'adults_price',
    'children_price',
    ];

    public function Flights()
    {
        return $this->hasMany(Flights::class,'flights_id');
    }
    public function FlightsReservation()
    {
        return $this->hasMany(FlightsReservation::class,'flights_reservations_id');
    }
}
