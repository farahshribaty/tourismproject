<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Hotel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    //public $timestamp = false;
    // protected $table='hotels';
    // protected $primaryKey='id';

    protected $fillable = [
        'name',
        'email',
        'location',
        'phone_number',
        'details',
        'rate',
        'num_of_ratings', 
        'website_url',
        'city_id',
        'type_id'
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
    public function photos()
    {
        return $this->hasMany(HotelPhoto::class,'hotel_id');
    }
    public function Facilities()
    {
        return $this->belongsToMany(Facilities::class,'hotels_facilities','hotel_id','facilities_id');
    }
    public function Room()
    {
        return $this->hasMany(Room::class,'room_id');
    }
    public function reviews()
    {
        return $this->belongsToMany(User::class,HotelReview::class,'hotel_id','user_id');
    }
    
}
