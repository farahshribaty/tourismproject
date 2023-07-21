<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'departure_date',
        'current_reserved_people',
        'price',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function reservation(){
        return $this->hasMany(TripsReservation::class,'date_id');
    }

    public function trip(){
        return $this->belongsTo(Trip::class,'trip_id');
    }
}
