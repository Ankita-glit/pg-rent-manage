<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_invoice_id',
        'payment_method',
        'amount',
        'payment_date',
        'payment_time',
        'transaction_id',
        'screenshot_path',
        'cash_receiver_name',
        'notes',
        'status',
        'rejection_reason',
        'verified_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(RentInvoice::class, 'rent_invoice_id');
    }

    public function verifiedByAdmin()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
