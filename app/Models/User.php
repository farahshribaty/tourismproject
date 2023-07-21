<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'wallet',
        'points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function attractionReviews()
    {
        return $this->belongsToMany(Attraction::class,AttractionReview::class,'user_id','attraction_id');
    }
    public function HotelReviews()
    {
        return $this->belongsToMany(Hotel::class,HotelReview::class,'user_id','hotel_id');
    }
    public function TripReviews()
    {
        return $this->belongsToMany(Trip::class,TripReview::class,'user_id','trip_id');
    }
    public function tripReservations()
    {
        return $this->belongsToMany(TripDate::class,TripsReservation::class,'user_id','date_id');
    }
    public function HotelReservation()
    {
        return $this->hasMany(HotelResevation::class,'user_id','hotel_id');
    }
    public function FlightsReservation()
    {
        return $this->hasMany(FlightsReservation::class,'flights_reservations_id');
    }

    public function favouriteTrips()
    {
        return $this->belongsToMany(Trip::class, TripFavourite::class, 'user_id', 'trip_id');
    }

    public function reviews()
    {
        return $this->hasMany(HotelReview::class,'user_id');
    }
}
