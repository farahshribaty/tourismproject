<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'details',
    ];

    public function attraction()
    {
        return $this->hasMany(Attraction::class,'attraction_type_id');
    }
}
