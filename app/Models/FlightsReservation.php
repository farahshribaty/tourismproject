<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class FlightsReservation extends Model
{
    use HasFactory,HasApiTokens,Notifiable;

    protected $fillable=[
        'user_id',
        'flights_times_id',
        'flight_class',
        'num_of_adults',
        'num_of_children',
        'payment',
        'Points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function FlightsTime()
    {
        return $this->belongsTo(FlightsTime::class,'flights_times_id');
    }

}
