<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class TripCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_admin_id',
        'name',
        'email',
        'phone_number',
        'country_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function admin()
    {
        return $this->belongsTo(TripAdmin::class,'trip_admin_id');
    }
}
