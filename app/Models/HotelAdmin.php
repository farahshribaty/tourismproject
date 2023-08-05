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
        'first_name',
        'last_name',
        'user_name',
        'email',
        'password',
        'phone_number',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'password',
    ];

    public function Hotel()
    {
        return $this->hasOne(Hotel::class,'id');
    }
}
