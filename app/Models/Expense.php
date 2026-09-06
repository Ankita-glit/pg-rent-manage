<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'title',
        'amount',
        'expense_date',
        'receipt_path',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public static function categories(): array
    {
        return [
            'repair_maintenance' => 'Repair & Maintenance',
            'electricity_utility' => 'Electricity & Utilities',
            'water_bill' => 'Water Bill',
            'cleaning_hygiene' => 'Cleaning & Hygiene Supplies',
            'furniture_fixture' => 'Furniture & Fixtures',
            'miscellaneous' => 'Miscellaneous Expense',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return static::categories()[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }
}
