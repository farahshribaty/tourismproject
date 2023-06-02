<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class RoomFeatures extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
    'room_id',
    'features_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
