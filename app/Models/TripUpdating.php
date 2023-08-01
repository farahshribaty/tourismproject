<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripUpdating extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_admin_id',
        'trip_company_id',
        'add_or_update',
        'accepted',
        'rejected',
        'seen',

        'country_id',
        'phone_number',
        'email',
        'name',
    ];

    public function admin()
    {
        return $this->belongsTo(TripAdmin::class,'trip_admin_id');
    }
}
