<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
    ];

    public function trips(){
        return $this->belongsToMany(Trip::class,TripActivitiesIncluded::class,'activity_id','trip_id');
    }
}
