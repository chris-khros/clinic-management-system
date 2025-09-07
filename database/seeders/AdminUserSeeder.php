<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Staff;
use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@healwell.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create admin staff profile
        Staff::create([
            'user_id' => $adminUser->id,
            'employee_id' => 'EMP000001',
            'full_name' => 'Admin User',
            'phone' => '+1234567890',
            'email' => 'admin@healwell.com',
            'qualifications' => 'System Administrator',
            'department' => 'Administration',
            'position' => 'System Administrator',
            'role' => 'admin',
            'hire_date' => now(),
            'is_active' => true,
        ]);

        // Create a doctor user
        $doctorUser = User::create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor@healwell.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        // Create doctor staff profile
        Staff::create([
            'user_id' => $doctorUser->id,
            'employee_id' => 'EMP000002',
            'full_name' => 'Dr. John Smith',
            'phone' => '+1234567891',
            'email' => 'doctor@healwell.com',
            'qualifications' => 'MBBS, MD - General Medicine',
            'department' => 'General Medicine',
            'position' => 'Senior Doctor',
            'role' => 'doctor',
            'hire_date' => now(),
            'is_active' => true,
        ]);

        // Create doctor profile
        Doctor::create([
            'user_id' => $doctorUser->id,
            'specialization' => 'General Medicine',
            'license_number' => 'DOC001',
            'phone' => '+1234567891',
            'address' => '123 Medical Center, City',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'max_appointments_per_day' => 20,
            'is_active' => true,
        ]);

        // Create receptionist user
        $receptionistUser = User::create([
            'name' => 'Jane Doe',
            'email' => 'receptionist@healwell.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Create receptionist staff profile
        Staff::create([
            'user_id' => $receptionistUser->id,
            'employee_id' => 'EMP000003',
            'full_name' => 'Jane Doe',
            'phone' => '+1234567892',
            'email' => 'receptionist@healwell.com',
            'qualifications' => 'Diploma in Medical Administration',
            'department' => 'Reception',
            'position' => 'Senior Receptionist',
            'role' => 'receptionist',
            'hire_date' => now(),
            'is_active' => true,
        ]);
    }
}
