<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttractionUpdating extends Model
{
    use HasFactory;

    protected $fillable = [
        'attraction_admin_id',
        'attraction_id',
        'add_or_update',
        'accepted',
        'rejected',
        'seen',

        'city_id',
        'attraction_type_id',
        'name',
        'email',
        'password',
        'location',
        'phone_number',
        'open_at',
        'close_at',
        'available_days',
        'child_ability_per_day',
        'adult_ability_per_day',
        'details',
        'website_url',
        'adult_price',
        'child_price',
        'points_added_when_booking',

    ];

    public function admin()
    {
        return $this->belongsTo(AttractionAdmin::class,'attraction_admin_id');
    }
}
