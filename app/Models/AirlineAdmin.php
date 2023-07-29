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
    'user_name',
    'password'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function Airline()
    {
        return $this->belongsTo(Airline::class,'airline_id');
    }
}
