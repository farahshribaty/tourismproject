<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Types extends Model
{
    use HasFactory,HasApiTokens,Notifiable;
    
    protected $table='types';
    protected $primaryKey='id';

    protected $fillable=[
        'name'
    ];

    public function Hotel(){
        return $this->hasMany(Hotel::class, 'type_id','id');
    }
}
