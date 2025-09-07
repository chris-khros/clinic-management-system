<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = Appointment::inRandomOrder()->take(20)->get();
        $services = Service::all();

        foreach ($appointments as $appt) {
            $subtotal = 0;
            $bill = Bill::create([
                'bill_number' => 'BILL-' . strtoupper(uniqid()),
                'bill_date' => Carbon::parse($appt->appointment_date)->toDateString(),
                'patient_id' => $appt->patient_id,
                'appointment_id' => $appt->id,
                'doctor_id' => $appt->doctor_id,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'notes' => null,
                'due_date' => Carbon::parse($appt->appointment_date)->addDays(7),
            ]);

            // Add 1-3 service items
            $itemsCount = rand(1, 3);
            for ($i = 0; $i < $itemsCount; $i++) {
                $service = $services->random();
                $qty = rand(1, 2);
                $lineTotal = $service->price * $qty;
                $subtotal += $lineTotal;

                BillItem::create([
                    'bill_id' => $bill->id,
                    'service_id' => $service->id,
                    'quantity' => $qty,
                    'unit_price' => $service->price,
                    'total_price' => $lineTotal,
                ]);
            }

            $tax = round($subtotal * 0.10, 2);
            $total = $subtotal + $tax;

            $bill->update([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
            ]);
        }
    }
}

