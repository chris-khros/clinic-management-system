<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: require at least one doctor
        if (Doctor::count() === 0) {
            // No doctors present; skip appointment seeding to avoid FK violations
            return;
        }

        $patients = Patient::inRandomOrder()->take(30)->get();
        $doctorId = Doctor::query()->inRandomOrder()->value('id');

        foreach ($patients as $patient) {
            $daysOffset = rand(-7, 14);
            $date = Carbon::today()->addDays($daysOffset);
            $time = Carbon::createFromTime(rand(9, 16), [0, 30][array_rand([0,1])], 0);
            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'appointment_date' => $date->format('Y-m-d'),
                'appointment_time' => $time->format('H:i:s'),
                'status' => ['scheduled','confirmed','in_progress','completed','cancelled','no_show'][array_rand(['scheduled','confirmed','in_progress','completed','cancelled','no_show'])],
                'reason' => 'Routine check-up',
                'notes' => null,
            ]);
        }
    }
}

