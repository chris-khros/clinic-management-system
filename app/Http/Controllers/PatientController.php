<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('patient_id', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_verified', $request->status === 'verified');
        }

        $patients = $query->latest()->paginate(10)->withQueryString();

        // Statistics
        $totalPatients = Patient::count();
        $newPatientsThisMonth = Patient::whereMonth('created_at', now()->month)->count();
        $unverifiedPatients = Patient::where('is_verified', false)->count();

        return view('patients.index', compact(
            'patients',
            'totalPatients',
            'newPatientsThisMonth',
            'unverifiedPatients'
        ));
    }

    public function create()
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        return view('patients.create', compact('bloodGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|max:20|unique:patients,phone',
            'email' => 'required|email|unique:patients,email',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo_data' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->photo_data) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->photo_data));
            $photoPath = 'patient-photos/' . Str::random(40) . '.png';
            Storage::disk('public')->put($photoPath, $imageData);
        }

        $patient = Patient::create([
            'patient_id' => 'PAT' . strtoupper(Str::random(8)),
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'photo' => $photoPath,
            'is_verified' => false,
        ]);

        // Send OTP for email verification
        $otpService = app(\App\Services\OtpService::class);
        $otpSent = $otpService->sendVerificationOtp($patient);

        if ($otpSent) {
            return redirect()->route('otp.verify-form', ['email' => $patient->email])
                ->with('success', 'Patient registered successfully. Please check your email for the verification code.');
        } else {
            return redirect()->route('patients.index')
                ->with('error', 'Patient registered but failed to send verification email. Please try resending the OTP.');
        }
    }

    public function showOtpForm(Patient $patient)
    {
        return view('patients.verify-otp', compact('patient'));
    }

    public function verifyOtp(Request $request, Patient $patient)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        if ($patient->otp === $request->otp && now()->lessThan($patient->otp_expires_at)) {
            $patient->update([
                'is_verified' => true,
                'otp' => null,
                'otp_expires_at' => null,
            ]);
            return redirect()->route('patients.index')->with('success', 'Patient verified successfully!');
        } else {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }
    }

    public function show(Patient $patient)
    {
        $patient->load(['appointments.doctor', 'consultations.doctor', 'bills', 'medicalRecords', 'documents']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $genders = ['male', 'female', 'other'];
        return view('patients.edit', compact('patient', 'bloodGroups', 'genders'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle photo upload
        $photoPath = $patient->photo;
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('patient-photos', 'public');
        }

        // Update patient
        $patient->update([
            'full_name' => $request->full_name,
            'photo' => $photoPath,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'medical_history' => $request->medical_history,
            'allergies' => $request->allergies,
            'blood_group' => $request->blood_group,
            'notes' => $request->notes,
        ]);

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        // Delete photo
        if ($patient->photo && Storage::disk('public')->exists($patient->photo)) {
            Storage::disk('public')->delete($patient->photo);
        }

        // Delete patient
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }

    public function verify(Patient $patient)
    {
        $patient->update([
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('patients.show', $patient)->with('success', 'Patient verified successfully.');
    }

    public function verifyAll()
    {
        Patient::where('is_verified', false)->update(['is_verified' => true]);

        return redirect()->route('patients.index')->with('success', 'All unverified patients have been verified successfully.');
    }


    public function uploadDocument(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type' => 'nullable|string|max:100',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('patient-documents/' . $patient->id, 'public');

        $document = \App\Models\PatientDocument::create([
            'patient_id' => $patient->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'document_type' => $validated['document_type'] ?? null,
            'is_verified' => false,
            'uploaded_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully.',
                'document' => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'document_type' => $document->document_type,
                    'file_type' => $document->file_type,
                    'file_size_kb' => round($document->file_size / 1024, 1),
                    'uploaded_at' => optional($document->uploaded_at)->toDateTimeString(),
                    'url' => asset('storage/' . $document->file_path),
                ],
            ]);
        }

        return redirect()->route('patients.show', $patient)->with('success', 'Document uploaded successfully.');
    }

}
