<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'attraction_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'attraction_id',
    ];

    public function attractions()
    {
        return $this->belongsTo(Attraction::class,'attraction_id');
    }
}
