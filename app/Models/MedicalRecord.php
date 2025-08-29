<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'patient_id',
        'record_type',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'is_public',
        'record_date',
    ];

    protected $casts = [
        'record_date' => 'datetime',
        'is_public' => 'boolean',
    ];

    /**
     * Get the consultation that owns the medical record.
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Get the patient that owns the medical record.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
