<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ConsultationSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = Appointment::whereIn('status', ['completed','in_progress','confirmed'])
            ->inRandomOrder()->take(20)->get();

        foreach ($appointments as $appt) {
            Consultation::create([
                'appointment_id' => $appt->id,
                'doctor_id' => $appt->doctor_id,
                'patient_id' => $appt->patient_id,
                'symptoms' => 'Headache, fatigue',
                'chief_complaint' => 'Headache',
                'duration' => 'acute',
                'vital_signs' => 'BP 120/80, HR 78',
                'physical_findings' => 'Normal exam',
                'primary_diagnosis' => 'Tension headache',
                'secondary_diagnosis' => null,
                'diagnosis' => 'Likely tension-type headache',
                'treatment_plan' => 'Hydration, rest, OTC analgesics',
                'medications' => 'Paracetamol 500mg',
                'prescription' => null,
                'notes' => null,
                'follow_up_date' => Carbon::now()->addDays(rand(7, 21))->format('Y-m-d'),
                'consultation_date' => Carbon::parse($appt->appointment_date),
                'consultation_started_at' => Carbon::parse($appt->appointment_date)->setTime(10,0,0),
                'consultation_completed_at' => Carbon::parse($appt->appointment_date)->setTime(10,30,0),
                'status' => 'completed',
            ]);
        }
    }
}

