<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Hotel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    //public $timestamp = false;
    protected $table='hotels';
    protected $primaryKey='id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'type_id'
    ];

    protected $hidden = [
        'password',
    ];

    public function Type(){
        return $this->belongsTo(Types::class, 'type_id','id');
    }
}
