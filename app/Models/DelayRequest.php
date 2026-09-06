<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelayRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_invoice_id',
        'renter_id',
        'requested_date',
        'reason',
        'status',
        'admin_comment',
    ];

    protected $casts = [
        'requested_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(RentInvoice::class, 'rent_invoice_id');
    }

    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
}
