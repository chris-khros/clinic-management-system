<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'document_type',
        'is_verified',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the patient that owns the document.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
