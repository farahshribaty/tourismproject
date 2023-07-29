<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Hotel extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='hotels';
    protected $primaryKey='id';

    protected $fillable = [
        'name',
        'email',
        'location',
        'phone_number',
        'details',
        'num_of_rooms',
        'rate',
        'stars',
        'num_of_ratings',
        'price_start_from',
        'website_url',
        'city_id',
        'type_id',
        'admin_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function Type(){
        return $this->belongsTo(Types::class, 'type_id');
    }
    public function City(){
        return $this->belongsTo(City::class, 'city_id');
    }
    public function photo()
    {
        return $this->hasMany(HotelPhoto::class,'hotel_id');
    }
    public function onePhoto()
    {
        return $this->hasOne(HotelPhoto::class,'hotel_id')->latest('id');
    }
    public function Facilities()
    {
        return $this->belongsToMany(Facilities::class,'hotels_facilities','hotel_id','facilities_id');
    }
    public function Room()
    {
        return $this->hasMany(Room::class,'hotel_id');
    }
    public function reviews()
    {
        return $this->hasMany(HotelReview::class,'hotel_id');
    }

    public function Admin()
    {
       return $this->hasOne(HotelAdmin::class,'hotel_admins_id');
    }

}
