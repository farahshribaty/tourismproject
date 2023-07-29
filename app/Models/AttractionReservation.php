<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attraction_id',
        'book_date',
        'adults',
        'children',
        'payment',
        'points_added',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
