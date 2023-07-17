<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
    ];

    public function trips(){
        return $this->belongsToMany(Trip::class,WhatIsIncluded::class,'service_id','trip_id');
    }
}
