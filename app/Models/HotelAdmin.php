<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class HotelAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table='hotel_admins';
    protected $primaryKey='id';

    protected $fillable = [
    'user_name',
    'password'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function Hotel()
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
}
