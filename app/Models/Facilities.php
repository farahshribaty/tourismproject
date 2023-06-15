<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Facilities extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable =[
     'Wifi',
     'Parking',
     'Transportation',
     'Formalization',
     'activities',
     'Meals'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function Hotels()
    {
        return $this->belongsToMany(Hotel::class,'hotels_facilities','facilities_id','hotel_id');
    }
}
