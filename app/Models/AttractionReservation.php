<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'attraction_id',
        'book_date',
        'adults',
        'children',
        'payment',
        'points_added',
        'seen',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
