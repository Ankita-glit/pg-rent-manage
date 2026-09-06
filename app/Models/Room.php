<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'room_number',
        'bed_capacity',
        'monthly_price',
        'status',
        'description',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function renters()
    {
        return $this->hasMany(User::class, 'assigned_room_id');
    }

    public function getOccupiedBedsCountAttribute()
    {
        return $this->renters()->where('status', 'active')->count();
    }

    public function getAvailableBedsCountAttribute()
    {
        return max(0, $this->bed_capacity - $this->occupied_beds_count);
    }
}
