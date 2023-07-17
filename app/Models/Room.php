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
     'num_of_ratings'
    ];

    public function Type(){
        return $this->belongsTo(RoomType::class, 'room_types_id');
    }
    public function Hotel(){
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
    public function Photo(){
        return $this->hasOne(TripPhoto::class,'trip_id')->latest('id');
    }
    public function Photos(){
        return $this->hasMany(RoomPhotos::class, 'room_id');
    }
    public function Features()
    {
        return $this->belongsToMany(Features::class,'room_features','room_id','features_id');
    }

    public function scopeWithAllInformation($query){
        $query->join('hotels','rooms.hotel_id','=','hotels.id')
            ->join('room_types','room_types.id','=','rooms.room_type')
            ->join('cities','hotels.city_id','=','cities.id')
            ->join('countries','countries.id','=','cities.country_id')
            ->join('room_photos','room_photos.room_id','=','rooms.id')
            ->select(['hotels.id as hotel_id',
                'rooms.id as room_id',
                'cities.name as city_name',
                'countries.name as country_name',
                'hotels.name',
                'room_types.name as room_type',
                'Price_for_night',
                'hotels.rate',
                'hotels.num_of_ratings',
                'room_photos.path as photo',
            ]);
    }

}
