<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Airline extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table='airlines';
    protected $primaryKey='id';

    protected $fillable = [
        'name',
        'email',
        'location',
        'phone_number',
        'rate',
        'country_id',
    ];

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}
