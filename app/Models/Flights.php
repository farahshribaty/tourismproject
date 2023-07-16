<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Flights extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='flights';
    protected $primaryKey='id';

    protected $fillable =[
        'flight_name',
        'flight_number',
        'airline_id',
        'from',
        'distination',
        'carry_on_bag',
        'checked_bag',
        'duration',
        'departure_time',
        'arrival_time',
        'available_seats',
        'flight_class',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'flight_id');
    }

}
