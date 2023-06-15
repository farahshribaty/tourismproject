<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPhotos extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'room_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'room_id',
    ];

    public function Room()
    {
        return $this->belongsTo(Room::class,'room_id');
    }
}
