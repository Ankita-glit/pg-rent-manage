<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'father_name',
        'mother_name',
        'email',
        'password',
        'role',
        'phone',
        'emergency_contact',
        'id_type',
        'id_number',
        'id_proof_path',
        'permanent_address',
        'college_office_name',
        'blood_group',
        'joining_date',
        'status',
        'assigned_room_id',
        'bed_number',
        'monthly_rent',
        'security_deposit',
        'rent_due_day',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joining_date' => 'date',
            'monthly_rent' => 'decimal:2',
            'security_deposit' => 'decimal:2',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRenter(): bool
    {
        return $this->role === 'renter';
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'assigned_room_id');
    }

    public function invoices()
    {
        return $this->hasMany(RentInvoice::class, 'renter_id');
    }

    public function delayRequests()
    {
        return $this->hasMany(DelayRequest::class, 'renter_id');
    }

    public function getTenureDurationAttribute(): string
    {
        if (!$this->joining_date) {
            return 'N/A';
        }

        $now = Carbon::now();
        if ($this->joining_date->gt($now)) {
            return 'Joining on ' . $this->joining_date->format('d M Y');
        }

        $diff = $this->joining_date->diffForHumans($now, [
            'parts' => 3,
            'join' => true,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
        ]);

        return $diff;
    }

    public function getTenureFormattedAttribute(): string
    {
        if (!$this->joining_date) {
            return 'N/A';
        }
        return $this->tenure_duration . ' (since ' . $this->joining_date->format('d M Y') . ')';
    }
}
