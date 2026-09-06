<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_number',
        'name',
        'description',
    ];

    public function rooms()
    {
        return $table = $this->hasMany(Room::class);
    }
}
