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
        'name','country_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    } 
}
