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
        'available_weight',
        'available_seats',
        'flight_class',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class,'airlines_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function from()
    {
        return $this->belongsTo(Country::class,'from');
    }
    public function destination()
    {
        return $this->belongsTo(Country::class,'distination');
    }
    public function Flights()
    {
        return $this->belongsTo(FlightsTime::class,'flights_id');
    }

}
