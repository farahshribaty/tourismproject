<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class HotelsFacilities extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
    'hotel_id',
    'facilities_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
