<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'license_number',
        'phone',
        'address',
        'start_time',
        'end_time',
        'max_appointments_per_day',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the user that owns the doctor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get today's appointments for the doctor.
     */
    public function todaysAppointments(): HasMany
    {
        return $this->appointments()
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time');
    }

    /**
     * Get the consultations for the doctor.
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get the bills for the doctor.
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
