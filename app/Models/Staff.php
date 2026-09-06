<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'designation',
        'phone',
        'email',
        'monthly_salary',
        'joining_date',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'monthly_salary' => 'decimal:2',
    ];

    public function salaries()
    {
        return $this->hasMany(StaffSalary::class);
    }
}
