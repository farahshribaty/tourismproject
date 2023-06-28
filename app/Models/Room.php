<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Room extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
     'room_type',
     'hotel_id',
     'details',
     'price_for_night',
     'rate',
     'num_of_ratings'
    ];
    
    public function Type(){
        return $this->belongsTo(RoomType::class, 'room_types_id');
    }
    public function Hotel(){
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
    public function photo(){
        return $this->hasMany(RoomPhotos::class, 'room_id');
    }
    public function Features()
    {
        return $this->belongsToMany(Features::class,'room_features','room_id','features_id');
    }

}
