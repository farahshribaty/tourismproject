<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'stars',
        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function trip(){
        return $this->belongsTo(Trip::class,'trip_id');
    }
}
