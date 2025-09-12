<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query
        if ($user->role === 'doctor') {
            $query = $user->doctor->consultations()->with(['appointment.patient', 'doctor.user']);
        } else {
            $query = Consultation::with(['appointment.patient', 'doctor.user']);
        }

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->whereHas('appointment', function ($q) use ($request) {
                $q->where('patient_id', $request->patient_id);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('consultation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('consultation_date', '<=', $request->date_to);
        }

        // Order by consultation date descending
        $query->orderBy('consultation_date', 'desc');

        $consultations = $query->paginate(10)->appends($request->query());

        return view('consultations.index', compact('consultations'));
    }

    public function create(Request $request)
    {
        $appointment_id = $request->query('appointment');
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($appointment_id);

        // Check if user is authorized to create consultation for this appointment
        $user = Auth::user();

        // Only doctors and admins can create consultations
        if (!in_array($user->role, ['doctor', 'admin'])) {
            abort(403, 'Only doctors and administrators can create consultations.');
        }

        if ($user->role === 'doctor' && $appointment->doctor_id !== $user->doctor?->id) {
            abort(403, 'You can only create consultations for your own appointments.');
        }

        return view('consultations.create', compact('appointment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'symptoms' => 'required|string',
            'chief_complaint' => 'nullable|string|max:255',
            'duration' => 'nullable|in:acute,subacute,chronic',
            'vital_signs' => 'nullable|string',
            'physical_findings' => 'nullable|string',
            'primary_diagnosis' => 'nullable|string|max:255',
            'secondary_diagnosis' => 'nullable|string|max:255',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'medications' => 'nullable|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:today',
            'status' => 'required|in:pending,completed,follow_up',
            'prescription_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'lab_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'other_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            // simplified single uploader support
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $appointment = Appointment::find($request->appointment_id);
        $user = Auth::user();

        // Only doctors and admins can create consultations
        if (!in_array($user->role, ['doctor', 'admin'])) {
            abort(403, 'Only doctors and administrators can create consultations.');
        }

        // Determine doctor_id - use from request if provided, otherwise from appointment
        $doctor_id = $request->doctor_id ?? $appointment->doctor_id;

        // Security check: ensure user can create consultation for this appointment
        if ($user->role === 'doctor' && $appointment->doctor_id !== $user->doctor?->id) {
            abort(403, 'You can only create consultations for your own appointments.');
        }

        // Create consultation
        $consultation = Consultation::create([
            'appointment_id' => $request->appointment_id,
            'doctor_id' => $doctor_id,
            'patient_id' => $appointment->patient_id,
            'symptoms' => $request->symptoms,
            'chief_complaint' => $request->chief_complaint,
            'duration' => $request->duration,
            'vital_signs' => $request->vital_signs,
            'physical_findings' => $request->physical_findings,
            'primary_diagnosis' => $request->primary_diagnosis,
            'secondary_diagnosis' => $request->secondary_diagnosis,
            'diagnosis' => $request->diagnosis,
            'treatment_plan' => $request->treatment_plan,
            'medications' => $request->medications,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
            'follow_up_date' => $request->follow_up_date,
            'consultation_date' => now(),
            'consultation_started_at' => now(),
            'consultation_completed_at' => now(),
            'status' => $request->status,
        ]);

        // Handle file uploads
        $this->handleFileUploads($request, $consultation);

        // Update appointment status
        $appointment->update(['status' => 'completed']);

        return redirect()->route('consultations.show', $consultation)->with('success', 'Consultation recorded successfully.');
    }

    private function handleFileUploads(Request $request, Consultation $consultation)
    {
        $uploadPath = 'consultations/' . $consultation->id;

        // Handle prescription files
        if ($request->hasFile('prescription_files')) {
            foreach ($request->file('prescription_files') as $file) {
                $this->storeMedicalRecord($consultation, $file, 'prescription', $uploadPath);
            }
        }

        // Handle lab result files
        if ($request->hasFile('lab_files')) {
            foreach ($request->file('lab_files') as $file) {
                $this->storeMedicalRecord($consultation, $file, 'lab_result', $uploadPath);
            }
        }

        // Handle other medical report files
        if ($request->hasFile('other_files')) {
            foreach ($request->file('other_files') as $file) {
                $this->storeMedicalRecord($consultation, $file, 'medical_report', $uploadPath);
            }
        }

        // Handle simplified single uploader (documents[] -> medical_report)
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $this->storeMedicalRecord($consultation, $file, 'medical_report', $uploadPath);
            }
        }
    }

    private function storeMedicalRecord(Consultation $consultation, $file, $recordType, $uploadPath)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($uploadPath, $filename, 'public');

        \App\Models\MedicalRecord::create([
            'consultation_id' => $consultation->id,
            'patient_id' => $consultation->patient_id,
            'record_type' => $recordType,
            'title' => $file->getClientOriginalName(),
            'description' => ucfirst($recordType) . ' uploaded during consultation',
            'file_path' => $filePath,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'is_public' => false,
            'record_date' => now(),
        ]);
    }

    public function show(Consultation $consultation)
    {
        // Security: Only allow doctors to view their own consultations or admins to view all
        $user = Auth::user();
        if ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id) {
            abort(403, 'You can only view your own consultations.');
        }

        $consultation->load(['patient', 'doctor', 'appointment', 'medicalRecords']);
        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation)
    {
        // Security: Only allow doctors to edit their own consultations or admins to edit all
        $user = Auth::user();
        if ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id) {
            abort(403, 'You can only edit your own consultations.');
        }

        $consultation->load(['appointment']);
        return view('consultations.edit', compact('consultation'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        // Security: Only allow doctors to update their own consultations or admins to update all
        $user = Auth::user();
        if ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id) {
            abort(403, 'You can only update your own consultations.');
        }

        $request->validate([
            'symptoms' => 'required|string',
            'chief_complaint' => 'nullable|string|max:255',
            'duration' => 'nullable|in:acute,subacute,chronic',
            'vital_signs' => 'nullable|string',
            'physical_findings' => 'nullable|string',
            'primary_diagnosis' => 'nullable|string|max:255',
            'secondary_diagnosis' => 'nullable|string|max:255',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'medications' => 'nullable|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:today',
            'status' => 'required|in:pending,completed,follow_up',
        ]);

        $consultation->update([
            'symptoms' => $request->symptoms,
            'chief_complaint' => $request->chief_complaint,
            'duration' => $request->duration,
            'vital_signs' => $request->vital_signs,
            'physical_findings' => $request->physical_findings,
            'primary_diagnosis' => $request->primary_diagnosis,
            'secondary_diagnosis' => $request->secondary_diagnosis,
            'diagnosis' => $request->diagnosis,
            'treatment_plan' => $request->treatment_plan,
            'medications' => $request->medications,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
            'follow_up_date' => $request->follow_up_date,
            'status' => $request->status,
        ]);

        return redirect()->route('consultations.show', $consultation)->with('success', 'Consultation updated successfully.');
    }

    public function destroy(Consultation $consultation)
    {
        // Security: Only allow doctors to delete their own consultations or admins to delete all
        $user = Auth::user();
        if ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id) {
            abort(403, 'You can only delete your own consultations.');
        }

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

    public function downloadMedicalRecord(\App\Models\MedicalRecord $medicalRecord)
    {
        // Security: Only allow doctors to download their own consultation files or admins to download all
        $user = Auth::user();
        if ($user->role === 'doctor' && $medicalRecord->consultation->doctor_id !== $user->doctor->id) {
            abort(403, 'You can only download files from your own consultations.');
        }

        $filePath = storage_path('app/public/' . $medicalRecord->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $medicalRecord->title);
    }
}
