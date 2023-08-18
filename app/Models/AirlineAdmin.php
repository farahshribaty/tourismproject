<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class AirlineAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table='airline_admins';
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

    public function Airline()
    {
        return $this->hasOne(Airline::class,'airline_admin_id');
    }
}
