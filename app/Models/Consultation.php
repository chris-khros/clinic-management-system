<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'patient_id',
        'symptoms',
        'chief_complaint',
        'duration',
        'vital_signs',
        'physical_findings',
        'primary_diagnosis',
        'secondary_diagnosis',
        'diagnosis',
        'treatment_plan',
        'medications',
        'prescription',
        'notes',
        'consultation_date',
        'consultation_started_at',
        'consultation_completed_at',
        'follow_up_date',
        'status',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'consultation_started_at' => 'datetime',
        'consultation_completed_at' => 'datetime',
        'follow_up_date' => 'date',
    ];

    /**
     * Get the appointment that owns the consultation.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the doctor that owns the consultation.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the patient that owns the consultation.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the medical records for the consultation.
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
