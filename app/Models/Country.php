<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Country extends Model
{

    use HasFactory,HasApiTokens,Notifiable;

    protected $table='countries';
    protected $primaryKey='id';

    protected $fillable=[
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function Addcities()
    {
        return $this->hasMany(City::class,'cities');
    }
}
