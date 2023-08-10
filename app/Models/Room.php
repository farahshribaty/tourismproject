<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Room extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table='rooms';
    protected $primaryKey='id';

    protected $fillable = [
     'room_type',
     'hotel_id',
     'details',
     'price_for_night',
     'rate',
     'num_of_ratings',
     'Sleeps',
     'Beds'
    ];

    public function Type(){
        return $this->belongsTo(RoomType::class, 'room_type');
    }

    public function Hotel(){
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function photo()
    {
        return $this->hasMany(RoomPhotos::class, 'room_id');
    }

    public function features()
    {
        return $this->belongsToMany(Features::class,'room_features','room_id', 'features_id');
    }

    public function HotelReservation()
    {
        return $this->belongsTo(HotelResevation::class,'hotel_resevations.id');
    }

    public function Reservations()
    {
        return $this->hasMany(HotelReservation::class,'room_id');
    }

}
