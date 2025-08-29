<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'doctor') {
            $consultations = $user->doctor->consultations()->with(['patient', 'appointment'])->paginate(10);
        } else {
            $consultations = Consultation::with(['patient', 'doctor', 'appointment'])->paginate(10);
        }

        return view('consultations.index', compact('consultations'));
    }

    public function create()
    {
        $appointments = Appointment::where('status', 'confirmed')
            ->whereDoesntHave('consultation')
            ->with(['patient', 'doctor'])
            ->get();

        return view('consultations.create', compact('appointments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'symptoms' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,follow_up',
        ]);

        $appointment = Appointment::find($request->appointment_id);

        // Create consultation
        Consultation::create([
            'appointment_id' => $request->appointment_id,
            'doctor_id' => $appointment->doctor_id,
            'patient_id' => $appointment->patient_id,
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'treatment_plan' => $request->treatment_plan,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
            'consultation_date' => now(),
            'status' => $request->status,
        ]);

        // Update appointment status
        $appointment->update(['status' => 'completed']);

        return redirect()->route('consultations.index')->with('success', 'Consultation recorded successfully.');
    }

    public function show(Consultation $consultation)
    {
        $consultation->load(['patient', 'doctor', 'appointment', 'medicalRecords']);
        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation)
    {
        $consultation->load(['appointment']);
        return view('consultations.edit', compact('consultation'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,follow_up',
        ]);

        $consultation->update([
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'treatment_plan' => $request->treatment_plan,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('consultations.index')->with('success', 'Consultation updated successfully.');
    }

    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        return redirect()->route('consultations.index')->with('success', 'Consultation deleted successfully.');
    }

    public function patientHistory(Patient $patient)
    {
        $consultations = $patient->consultations()
            ->with(['doctor', 'appointment'])
            ->orderBy('consultation_date', 'desc')
            ->get();

        return view('consultations.patient-history', compact('patient', 'consultations'));
    }

    public function complete(Consultation $consultation)
    {
        $consultation->update(['status' => 'completed']);
        return redirect()->route('consultations.show', $consultation)->with('success', 'Consultation marked as completed.');
    }

    public function followUp(Consultation $consultation)
    {
        $consultation->update(['status' => 'follow_up']);
        return redirect()->route('consultations.show', $consultation)->with('success', 'Consultation marked for follow-up.');
    }
}
