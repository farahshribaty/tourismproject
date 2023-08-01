<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class TripAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_name',
        'password',
        'full_name',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    public function tripCompany()
    {
        return $this->hasOne(TripCompany::class,'trip_admin_id');
    }

}
