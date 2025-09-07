<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Seed 50 patients without photos/documents
        for ($i = 0; $i < 50; $i++) {
            Patient::create([
                'patient_id' => 'PAT' . strtoupper(Str::random(8)),
                'full_name' => $faker->name(),
                'photo' => null,
                'date_of_birth' => Carbon::now()->subYears(rand(1, 80))->subDays(rand(0, 365))->format('Y-m-d'),
                'gender' => $faker->randomElement(['male', 'female']),
                'phone' => $faker->unique()->numerify('9#########'),
                'email' => $faker->unique()->safeEmail(),
                'address' => $faker->address(),
                'emergency_contact_name' => $faker->name(),
                'emergency_contact_phone' => $faker->numerify('9#########'),
                'medical_history' => $faker->boolean(30) ? $faker->sentence(8) : null,
                'allergies' => $faker->boolean(20) ? $faker->randomElement(['Penicillin', 'Peanuts', 'Dust', 'Shellfish']) : null,
                'blood_group' => $faker->boolean(60) ? $faker->randomElement(['A+','A-','B+','B-','AB+','AB-','O+','O-']) : null,
                'notes' => $faker->boolean(30) ? $faker->sentence(10) : null,
                'is_verified' => $faker->boolean(80),
                'email_verified_at' => null,
                'otp' => null,
                'otp_expires_at' => null,
            ]);
        }
    }
}

