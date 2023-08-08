<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Attraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'attraction_type_id',
        'attraction_admin_id',
        'name',
        'email',
        'password',
        'location',
        'phone_number',
        'rete',
        'num_of_ratings',
        'details',
        'open_at',
        'close_at',
        'available_days',
        'website_url',
        'adult_price',
        'child_price',
        'child_ability_per_day',
        'adult_ability_per_day',
        'points_added_when_booking',
    ];

    protected $hidden = [
        'password',
        'attraction_admin_id',
        'created_at',
        'updated_at',
    ];


    public function type()
    {
        return $this->belongsTo(AttractionType::class,'attraction_type_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public function photos()
    {
        return $this->hasMany(AttractionPhoto::class,'attraction_id');
    }

    public function photo()
    {
        return $this->hasOne(AttractionPhoto::class,'attraction_id')->latest('id');
    }

    public function reviews()
    {
        return $this->belongsToMany(User::class,AttractionReview::class,'attraction_id','user_id');
    }

    public function admin()
    {
        return $this->belongsTo(AttractionAdmin::class,'attraction_admin_id');
    }

    public function followers()
    {
        return $this->hasMany(AttractionFavourite::class,'attraction_id');
    }
}
