<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Airline extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='airlines';
    protected $primaryKey='id';

    protected $fillable = [
        'name',
        'email',
        'location',
        'phone_number',
        'rate',
        'path',
        'country_id',
        'admin_id'
    ];

    public function flights()
    {
        return $this->hasMany(Flights::class, 'airline_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function Admin()
    {
       return $this->belongsTo(AirlineAdmin::class,'admin_id');
    }
}
