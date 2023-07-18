<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class HotelResevation extends Model
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='hotel_resevations';
    protected $primaryKey='id';

    protected $fillable =[
        'user_id',
        'hotel_id',
        'room_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'price',
    ];


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function room(){
        return $this->hasMany(Room::class,'room_id');
    }

}
