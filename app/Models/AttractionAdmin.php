<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class AttractionAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_name',
        'password',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    public function attraction()
    {
        return $this->hasOne(Attraction::class,'attraction_admin_id');
    }
}
