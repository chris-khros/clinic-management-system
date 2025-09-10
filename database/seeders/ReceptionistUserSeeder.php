<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class ReceptionistUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create receptionist user
        $receptionist = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'receptionist@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Create staff profile for receptionist
        Staff::create([
            'user_id' => $receptionist->id,
            'employee_id' => 'EMP001',
            'full_name' => 'Sarah Johnson',
            'phone' => '+1-555-0123',
            'email' => 'receptionist@clinic.com',
            'department' => 'Front Office',
            'position' => 'Receptionist',
            'role' => 'receptionist',
            'hire_date' => now()->subMonths(6),
            'is_active' => true,
        ]);

        // Create another receptionist for testing
        $receptionist2 = User::create([
            'name' => 'Mike Wilson',
            'email' => 'mike.wilson@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        Staff::create([
            'user_id' => $receptionist2->id,
            'employee_id' => 'EMP002',
            'full_name' => 'Mike Wilson',
            'phone' => '+1-555-0125',
            'email' => 'mike.wilson@clinic.com',
            'department' => 'Front Office',
            'position' => 'Senior Receptionist',
            'role' => 'receptionist',
            'hire_date' => now()->subYear(),
            'is_active' => true,
        ]);
    }
}
