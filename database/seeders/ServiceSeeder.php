<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'General Consultation',
                'description' => 'Basic medical consultation with a doctor',
                'price' => 50.00,
                'category' => 'consultation',
                'is_active' => true,
            ],
            [
                'name' => 'Specialist Consultation',
                'description' => 'Consultation with a specialist doctor',
                'price' => 100.00,
                'category' => 'consultation',
                'is_active' => true,
            ],
            [
                'name' => 'Blood Test',
                'description' => 'Complete blood count and basic blood tests',
                'price' => 25.00,
                'category' => 'laboratory',
                'is_active' => true,
            ],
            [
                'name' => 'X-Ray',
                'description' => 'Basic X-Ray examination',
                'price' => 80.00,
                'category' => 'radiology',
                'is_active' => true,
            ],
            [
                'name' => 'Ultrasound',
                'description' => 'Ultrasound examination',
                'price' => 120.00,
                'category' => 'radiology',
                'is_active' => true,
            ],
            [
                'name' => 'ECG',
                'description' => 'Electrocardiogram examination',
                'price' => 60.00,
                'category' => 'cardiology',
                'is_active' => true,
            ],
            [
                'name' => 'Vaccination',
                'description' => 'Basic vaccination service',
                'price' => 30.00,
                'category' => 'vaccination',
                'is_active' => true,
            ],
            [
                'name' => 'Minor Surgery',
                'description' => 'Minor surgical procedure',
                'price' => 500.00,
                'category' => 'surgery',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
