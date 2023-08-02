<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelUpdating extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_admins_id',
        'hotel_id',
        'add_or_update',
        'accepted',
        'rejected',
        'seen',
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

    public function admin()
    {
        return $this->belongsTo(HotelAdmin::class,'hotel_admins_id');
    }

}
