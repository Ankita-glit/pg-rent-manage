<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PgForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'template_file_path',
        'is_active',
        'due_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_date' => 'date',
    ];

    public function submissions()
    {
        return $this->hasMany(RenterFormSubmission::class, 'pg_form_id');
    }

    public function submissionForUser($userId)
    {
        return $this->submissions()->where('renter_id', $userId)->first();
    }
}
