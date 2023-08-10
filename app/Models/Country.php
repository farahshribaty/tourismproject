<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Country extends Model
{

    use HasFactory,HasApiTokens,Notifiable;

    protected $table='countries';
    protected $primaryKey='id';

    protected $fillable=[
        'name',
        'path'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function cities()
    {
        return $this->hasMany(City::class,'cities');
    }

    public function flights()
    {
        return $this->hasMany(Flights::class,'flights_id');
    }
    public function airline()
    {
        return $this->hasMany(Airline::class,'airlines_id');
    }
}
