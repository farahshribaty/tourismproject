<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class HotelReservation extends Model
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='hotel_reservations';
    protected $primaryKey='id';

    protected $fillable =[
        'user_id',
        'first_name',
        'last_name',
        'hotel_id',
        'room_id',
        'check_in',
        'check_out',
        'num_of_adults',
        'num_of_children',
        'payment',
        'points_added',
    ];


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function room(){
        return $this->hasMany(Room::class,'room_id');
    }
    public function hotel(){
        return $this->belongsTo(hotel::class,'hotel_id');
    }
}
