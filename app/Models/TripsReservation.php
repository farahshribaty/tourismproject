<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripsReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_id',
        'user_id',
        'child',
        'adult',
        'points_added',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'active',
    ];

    public function date(){
        return $this->belongsTo(TripDate::class,'date_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function travelers(){
        return $this->hasMany(TripTraveler::class,'reservation_id');
    }
}
