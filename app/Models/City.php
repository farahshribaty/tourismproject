<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class City extends Model
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='cities';
    protected $primaryKey='id';

    protected $fillable=[
        'name','country_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function countries()
    {
        return $this->belongsTo(Country::class,'countries');
    }

    public function Attractions()
    {
        return $this->hasMany(Attraction::class,'city_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function Hotel()
    {
        return $this->hasMany(Hotel::class,'hotel_id');
    }

    public function trips(){
        return $this->hasMany(Trip::class,'city_id');
    }
}
