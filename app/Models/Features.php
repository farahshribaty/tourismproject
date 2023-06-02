<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Features extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
  
    protected $fillable =[
        'Housekeeping',
        'Telephone',
        'Wake-up service',
        'Private bathrooms',
        'Hair dryer',
       ];
   
       protected $hidden = [
           'created_at',
           'updated_at',
       ];
   
       public function Room()
       {
           return $this->belongsToMany(Room::class,'room_features','features_id','room_id');
       }
}
