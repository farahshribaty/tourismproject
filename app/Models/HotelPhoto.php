<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'hotel_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'hotel_id',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
}
