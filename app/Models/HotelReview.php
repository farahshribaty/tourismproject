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
        'rate',
        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
