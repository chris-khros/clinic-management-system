<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            ReceptionistUserSeeder::class,
            ServiceSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
            ConsultationSeeder::class,
            BillingSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
