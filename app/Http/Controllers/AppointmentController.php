<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\AppointmentLock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Atomically enforce limits and locks
        DB::beginTransaction();
        try {
            // Check for existing appointment
            $hasExisting = Appointment::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();
            if ($hasExisting) {
                DB::rollBack();
                return back()->withErrors(['appointment_time' => 'This time slot is already booked.']);
            }

            // Check active lock
            $activeLock = AppointmentLock::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('locked_until', '>', now())
                ->lockForUpdate()
                ->first();
            if ($activeLock) {
                DB::rollBack();
                return back()->withErrors(['appointment_time' => 'This time slot is currently held by another user.']);
            }

            // Daily and weekly limits
            $doctor = Doctor::findOrFail($request->doctor_id);
            $dailyAppointments = Appointment::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->count();
            if ($doctor->max_appointments_per_day && $dailyAppointments >= $doctor->max_appointments_per_day) {
                DB::rollBack();
                return back()->withErrors(['appointment_date' => 'Doctor has reached maximum appointments for this day.']);
            }

            $weekStart = \Carbon\Carbon::parse($request->appointment_date)->startOfWeek();
            $weekEnd = \Carbon\Carbon::parse($request->appointment_date)->endOfWeek();
            if (property_exists($doctor, 'max_appointments_per_week') && $doctor->max_appointments_per_week) {
                $weeklyCount = Appointment::where('doctor_id', $request->doctor_id)
                    ->whereBetween('appointment_date', [$weekStart, $weekEnd])
                    ->where('status', '!=', 'cancelled')
                    ->lockForUpdate()
                    ->count();
                if ($weeklyCount >= $doctor->max_appointments_per_week) {
                    DB::rollBack();
                    return back()->withErrors(['appointment_date' => 'Doctor has reached maximum appointments for this week.']);
                }
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

            DB::commit();
            return redirect()->route('appointments.index')->with('success', 'Appointment scheduled successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to schedule appointment.']);
        }
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

        DB::beginTransaction();
        try {
            $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $appointment->id)
                ->lockForUpdate()
                ->exists();
            if ($existingAppointment) {
                DB::rollBack();
                return back()->withErrors(['appointment_time' => 'This time slot is already booked.']);
            }

            $activeLock = AppointmentLock::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $request->appointment_date)
                ->where('appointment_time', $request->appointment_time)
                ->where('locked_until', '>', now())
                ->lockForUpdate()
                ->first();
            if ($activeLock) {
                DB::rollBack();
                return back()->withErrors(['appointment_time' => 'This time slot is currently held by another user.']);
            }

            $appointment->update([
                'doctor_id' => $request->doctor_id,
                'patient_id' => $request->patient_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update appointment.']);
        }
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

        $locks = AppointmentLock::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->date)
            ->where('locked_until', '>', now())
            ->get(['appointment_time', 'locked_until']);

        return response()->json([
            'appointments' => $appointments,
            'locks' => $locks,
        ]);
    }

    public function lock(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'ttl_seconds' => 'nullable|integer|min:30|max:600',
        ]);

        $ttl = $request->input('ttl_seconds', 180);
        $lockedUntil = now()->addSeconds($ttl);

        try {
            $lock = DB::transaction(function () use ($request, $lockedUntil) {
                $exists = Appointment::where('doctor_id', $request->doctor_id)
                    ->where('appointment_date', $request->appointment_date)
                    ->where('appointment_time', $request->appointment_time)
                    ->where('status', '!=', 'cancelled')
                    ->lockForUpdate()
                    ->exists();
                if ($exists) {
                    return null;
                }

                $activeLock = AppointmentLock::where('doctor_id', $request->doctor_id)
                    ->where('appointment_date', $request->appointment_date)
                    ->where('appointment_time', $request->appointment_time)
                    ->where('locked_until', '>', now())
                    ->lockForUpdate()
                    ->first();
                if ($activeLock) {
                    return false;
                }

                return AppointmentLock::updateOrCreate(
                    [
                        'doctor_id' => $request->doctor_id,
                        'appointment_date' => $request->appointment_date,
                        'appointment_time' => $request->appointment_time,
                    ],
                    [
                        'locked_until' => $lockedUntil,
                        'held_by' => optional(Auth::user())->id,
                    ]
                );
            });
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'Lock failed'], 500);
        }

        if ($lock === null) {
            return response()->json(['ok' => false, 'reason' => 'already_booked'], 409);
        }
        if ($lock === false) {
            return response()->json(['ok' => false, 'reason' => 'locked'], 409);
        }

        return response()->json(['ok' => true, 'locked_until' => $lock->locked_until]);
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        AppointmentLock::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->delete();

        return response()->json(['ok' => true]);
    }
}
