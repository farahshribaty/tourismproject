<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripUpdating extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_admin_id',
        'country_id',
        'add_or_update',
        'accepted',
        'rejected',
        'seen',
        'phone_number',
        'email',
        'name',
        'trip_company_id'
    ];

    public function admin()
    {
        return $this->belognsTo(TripAdmin::class,'trip_admin_id');
    }
}
