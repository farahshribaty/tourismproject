<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attraction_id',
        'stars',
        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function attraction()
    {
        return $this->belongsTo(Attraction::class,'attraction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}
