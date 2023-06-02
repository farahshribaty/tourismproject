<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'stars',
        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
