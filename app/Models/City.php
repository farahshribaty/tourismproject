<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class City extends Model
{
    use HasFactory,HasApiTokens,Notifiable;

    protected $fillable=[
        'name'
    ];

    public function Addcities()
    {
        return $this->belongsTo(Country::class,'countries');
    } 
}
