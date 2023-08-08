<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionFavourite extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'attraction_id',
    ];
}
