<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class RoomType extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table='room_types';
    protected $primaryKey='id';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function Room(){
        return $this->hasMany(Room::class, 'room_id');
    }

}
