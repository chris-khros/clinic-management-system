<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'doctor') {
            $appointments = $user->doctor->appointments()->with(['patient', 'doctor'])->paginate(10);
        } else {
            $appointments = Appointment::with(['patient', 'doctor'])->paginate(10);
        }

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $doctors = Doctor::where('is_active', true)->get();
        $patients = Patient::where('is_verified', true)->get();
        return view('appointments.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Check for double booking
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked.']);
        }

        // Check daily limit for doctor
        $doctor = Doctor::find($request->doctor_id);
        $dailyAppointments = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($dailyAppointments >= $doctor->max_appointments_per_day) {
            return back()->withErrors(['appointment_date' => 'Doctor has reached maximum appointments for this day.']);
        }

        // Create appointment
        Appointment::create([
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'scheduled',
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment scheduled successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'consultation']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $doctors = Doctor::where('is_active', true)->get();
        $patients = Patient::where('is_verified', true)->get();
        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Check for double booking (excluding current appointment)
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked.']);
        }

        // Update appointment
        $appointment->update([
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Cancelled by admin',
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully.');
    }

    public function confirm(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment confirmed successfully.');
    }

    public function cancel(Appointment $appointment, Request $request)
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully.');
    }

    public function complete(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'completed',
        ]);

        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment marked as completed.');
    }

    public function calendar()
    {
        $user = Auth::user();
        
        if ($user->role === 'doctor') {
            $appointments = $user->doctor->appointments()
                ->with('patient')
                ->where('appointment_date', '>=', now()->subDays(30))
                ->where('appointment_date', '<=', now()->addDays(30))
                ->get();
        } else {
            $appointments = Appointment::with(['patient', 'doctor'])
                ->where('appointment_date', '>=', now()->subDays(30))
                ->where('appointment_date', '<=', now()->addDays(30))
                ->get();
        }

        return view('appointments.calendar', compact('appointments'));
    }

    public function getDoctorSchedule(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
        ]);

        $appointments = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get(['appointment_time']);

        return response()->json($appointments);
    }
}
