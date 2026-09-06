<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'renter_id',
        'room_id',
        'month_year',
        'amount',
        'due_date',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payment()
    {
        return $this->hasOne(RentPayment::class);
    }

    public function delayRequests()
    {
        return $this->hasMany(DelayRequest::class);
    }

    public function latestDelayRequest()
    {
        return $this->hasOne(DelayRequest::class)->latestOfMany();
    }
}
