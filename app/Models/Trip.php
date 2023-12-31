<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_company_id',
        'destination',
        'description',
        'details',
        'days_number',
        'max_persons',
        'rate',
        'num_of_ratings',
        'start_age',
        'end_age',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function destination(){
        return $this->belongsTo(City::class,'destination');
    }

    public function services(){
        return $this->belongsToMany(TripService::class,WhatIsIncluded::class,'trip_id','service_id');
    }

    public function activities(){
        return $this->belongsToMany(TripActivity::class,TripActivitiesIncluded::class,'trip_id','activity_id');
    }

    public function days(){
        return $this->hasMany(TripDay::class,'trip_id');
    }

    public function dates(){
        return $this->hasMany(TripDate::class,'trip_id');
    }

    public function photos(){
        return $this->hasMany(TripPhoto::class,'trip_id');
    }

    // give me only the trips that have at least one departure in the future and at least one seat available
    public function scopeAvailableTrips($query){
        return $query->whereHas('dates',function($query){
            $date = Carbon::now()->addDays(1);
            $query->whereRaw('current_reserved_people < max_persons')
                ->where('departure_date','>',$date);
        });
    }

    public function photo()
    {
        return $this->hasOne(TripPhoto::class,'trip_id')->latest('id');
    }

    public function offer()
    {
        return $this->hasMany(TripOffer::class,'trip_id')->latest('id');
    }

    public function offers()
    {
        return $this->hasMany(TripOffer::class,'trip_id');
    }

    public function review()
    {
        return $this->belongsToMany(User::class,TripReview::class,'trip_id','user_id');
    }

    public function followers()
    {
        return $this->hasMany(TripFavourite::class,'trip_id');
    }

}
