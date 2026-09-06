<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenterFormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'pg_form_id',
        'renter_id',
        'response_notes',
        'submitted_file_path',
        'status',
        'admin_feedback',
    ];

    public function form()
    {
        return $this->belongsTo(PgForm::class, 'pg_form_id');
    }

    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
}
