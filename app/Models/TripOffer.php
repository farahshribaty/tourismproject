<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'percentage_off',
        'active',
        'offer_end',
    ];

    protected $hidden = [
        'active',
        'created_at',
        'updated_at',
        'trip_id',
    ];




}
