<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Bill;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard();
            case 'doctor':
                return $this->doctorDashboard();
            case 'receptionist':
                return $this->receptionistDashboard();
            case 'nurse':
                return $this->nurseDashboard();
            case 'pharmacist':
                return $this->pharmacistDashboard();
            default:
                return redirect()->route('login');
        }
    }

    private function adminDashboard()
    {
        $data = [
            'total_staff' => Staff::count(),
            'total_doctors' => Doctor::count(),
            'total_patients' => Patient::count(),
            'total_appointments' => Appointment::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'total_bills' => Bill::count(),
            'total_revenue' => Bill::where('payment_status', 'paid')->sum('total_amount'),
            'pending_bills' => Bill::where('payment_status', 'pending')->count(),
        ];

        return view('dashboard.admin', compact('data'));
    }

    private function doctorDashboard()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return redirect()->route('login');
        }

        $data = [
            'today_appointments' => $doctor->todaysAppointments()->count(),
            'total_patients' => Patient::count(),
            'pending_consultations' => $doctor->consultations()->where('status', 'pending')->count(),
            'completed_consultations' => $doctor->consultations()->where('status', 'completed')->count(),
            'todays_appointments_list' => $doctor->todaysAppointments()->with('patient')->get(),
        ];

        return view('dashboard.doctor', compact('data'));
    }

    private function receptionistDashboard()
    {
        $data = [
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'total_patients' => Patient::count(),
            'pending_appointments' => Appointment::where('status', 'scheduled')->count(),
            'total_doctors' => Doctor::count(),
            'todays_appointments_list' => Appointment::whereDate('appointment_date', today())
                ->with(['patient', 'doctor'])->get(),
        ];

        return view('dashboard.receptionist', compact('data'));
    }

    private function nurseDashboard()
    {
        $data = [
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'total_patients' => Patient::count(),
            'pending_consultations' => Appointment::where('status', 'in_progress')->count(),
        ];

        return view('dashboard.nurse', compact('data'));
    }

    private function pharmacistDashboard()
    {
        $data = [
            'total_patients' => Patient::count(),
            'pending_prescriptions' => Bill::where('payment_status', 'pending')->count(),
        ];

        return view('dashboard.pharmacist', compact('data'));
    }

    /**
     * Income Summary Reports
     */
    public function incomeSummary(Request $request)
    {
        $query = Bill::with(['patient', 'doctor.user']);

        // Apply filters
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('report_type')) {
            $reportType = $request->report_type;
            $now = Carbon::now();

            switch ($reportType) {
                case 'daily':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'weekly':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'yearly':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }

        $bills = $query->orderBy('created_at', 'desc')->get();
        $doctors = Doctor::with('user')->get();

        // Calculate summary statistics
        $totalRevenue = $bills->sum('total_amount');
        $totalBills = $bills->count();
        $paidBills = $bills->where('payment_status', 'paid')->count();
        $pendingBills = $bills->where('payment_status', 'pending')->count();
        $partialBills = $bills->where('payment_status', 'partial')->count();

        // Group by date for chart data
        $dailyData = $bills->groupBy(function ($bill) {
            return $bill->created_at->format('Y-m-d');
        })->map(function ($dayBills) {
            return [
                'date' => $dayBills->first()->created_at->format('M d, Y'),
                'revenue' => $dayBills->sum('total_amount'),
                'count' => $dayBills->count()
            ];
        })->values();

        return response()->json([
            'bills' => $bills,
            'doctors' => $doctors,
            'totalRevenue' => $totalRevenue,
            'totalBills' => $totalBills,
            'paidBills' => $paidBills,
            'pendingBills' => $pendingBills,
            'partialBills' => $partialBills,
            'dailyData' => $dailyData
        ]);
    }

    /**
     * Patient Flow Reports
     */
    public function patientFlow(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user']);

        // Apply filters
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        if ($request->filled('report_type')) {
            $reportType = $request->report_type;
            $now = Carbon::now();

            switch ($reportType) {
                case 'daily':
                    $query->whereDate('appointment_date', $now->toDateString());
                    break;
                case 'weekly':
                    $query->whereBetween('appointment_date', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereMonth('appointment_date', $now->month)
                          ->whereYear('appointment_date', $now->year);
                    break;
                case 'yearly':
                    $query->whereYear('appointment_date', $now->year);
                    break;
            }
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->get();
        $doctors = Doctor::with('user')->get();

        // Calculate summary statistics
        $totalAppointments = $appointments->count();
        $completedAppointments = $appointments->where('status', 'completed')->count();
        $pendingAppointments = $appointments->where('status', 'pending')->count();
        $cancelledAppointments = $appointments->where('status', 'cancelled')->count();

        // Group by date for chart data
        $dailyData = $appointments->groupBy(function ($appointment) {
            return $appointment->appointment_date->format('Y-m-d');
        })->map(function ($dayAppointments) {
            return [
                'date' => $dayAppointments->first()->appointment_date->format('M d, Y'),
                'appointments' => $dayAppointments->count(),
                'completed' => $dayAppointments->where('status', 'completed')->count(),
                'pending' => $dayAppointments->where('status', 'pending')->count(),
                'cancelled' => $dayAppointments->where('status', 'cancelled')->count()
            ];
        })->values();

        // Patient volume trends (last 12 months)
        $monthlyTrends = Appointment::select(
            DB::raw('YEAR(appointment_date) as year'),
            DB::raw('MONTH(appointment_date) as month'),
            DB::raw('COUNT(*) as total_appointments'),
            DB::raw('COUNT(DISTINCT patient_id) as unique_patients')
        )
        ->where('appointment_date', '>=', Carbon::now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get()
        ->map(function ($item) {
            return [
                'month' => Carbon::create($item->year, $item->month)->format('M Y'),
                'appointments' => $item->total_appointments,
                'unique_patients' => $item->unique_patients
            ];
        });

        return response()->json([
            'appointments' => $appointments,
            'doctors' => $doctors,
            'totalAppointments' => $totalAppointments,
            'completedAppointments' => $completedAppointments,
            'pendingAppointments' => $pendingAppointments,
            'cancelledAppointments' => $cancelledAppointments,
            'dailyData' => $dailyData,
            'monthlyTrends' => $monthlyTrends
        ]);
    }

    /**
     * Export Income Summary to CSV
     */
    public function exportIncomeSummary(Request $request)
    {
        $query = Bill::with(['patient', 'doctor.user']);

        // Apply same filters as income summary
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bills = $query->orderBy('created_at', 'desc')->get();

        $filename = 'income_summary_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // CSV Headers
        fputcsv($file, [
            'Bill ID',
            'Bill Number',
            'Patient Name',
            'Doctor Name',
            'Date',
            'Subtotal',
            'Tax Amount',
            'Discount Amount',
            'Total Amount',
            'Payment Status',
            'Payment Method'
        ]);

        // CSV Data
        foreach ($bills as $bill) {
            fputcsv($file, [
                $bill->id,
                $bill->bill_number,
                $bill->patient->full_name,
                $bill->doctor ? $bill->doctor->user->name : 'N/A',
                $bill->created_at->format('Y-m-d'),
                $bill->subtotal,
                $bill->tax_amount,
                $bill->discount_amount,
                $bill->total_amount,
                $bill->payment_status,
                $bill->payment_method ?? 'N/A'
            ]);
        }

        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Export Patient Flow to CSV
     */
    public function exportPatientFlow(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user']);

        // Apply same filters as patient flow
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        $filename = 'patient_flow_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // CSV Headers
        fputcsv($file, [
            'Appointment ID',
            'Patient Name',
            'Doctor Name',
            'Appointment Date',
            'Appointment Time',
            'Status',
            'Reason',
            'Notes'
        ]);

        // CSV Data
        foreach ($appointments as $appointment) {
            fputcsv($file, [
                $appointment->id,
                $appointment->patient->full_name,
                $appointment->doctor ? $appointment->doctor->user->name : 'N/A',
                $appointment->appointment_date->format('Y-m-d'),
                $appointment->appointment_time,
                $appointment->status,
                $appointment->reason,
                $appointment->notes ?? 'N/A'
            ]);
        }

        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Email Reports
     */
    public function emailReport(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'report_type' => 'required|in:income,patient_flow'
        ]);

        // Generate the report data
        if ($request->report_type === 'income') {
            $filename = 'income_summary_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $data = $this->generateIncomeReportData($request);
        } else {
            $filename = 'patient_flow_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $data = $this->generatePatientFlowReportData($request);
        }

        // Create CSV file
        $filepath = storage_path('app/exports/' . $filename);
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');
        fputcsv($file, $data['headers']);
        foreach ($data['rows'] as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        // Send email with attachment (branded HTML)
        try {
            Mail::send('emails.report', ['reportType' => $request->report_type], function ($message) use ($request, $filepath, $filename) {
                $message->to($request->email)
                        ->subject(config('app.name') . ' - Report')
                        ->attach($filepath, ['as' => $filename]);
            });

            // Clean up file
            unlink($filepath);

            return response()->json([
                'success' => true,
                'message' => 'Report sent successfully to ' . $request->email
            ]);
        } catch (\Exception $e) {
            // Clean up file on error
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate income report data for CSV
     */
    private function generateIncomeReportData(Request $request)
    {
        $query = Bill::with(['patient', 'doctor.user']);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bills = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Bill ID', 'Bill Number', 'Patient Name', 'Doctor Name', 'Date',
            'Subtotal', 'Tax Amount', 'Discount Amount', 'Total Amount',
            'Payment Status', 'Payment Method'
        ];

        $rows = $bills->map(function ($bill) {
            return [
                $bill->id,
                $bill->bill_number,
                $bill->patient->full_name,
                $bill->doctor ? $bill->doctor->user->name : 'N/A',
                $bill->created_at->format('Y-m-d'),
                $bill->subtotal,
                $bill->tax_amount,
                $bill->discount_amount,
                $bill->total_amount,
                $bill->payment_status,
                $bill->payment_method ?? 'N/A'
            ];
        })->toArray();

        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * Generate patient flow report data for CSV
     */
    private function generatePatientFlowReportData(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user']);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->get();

        $headers = [
            'Appointment ID', 'Patient Name', 'Doctor Name', 'Appointment Date',
            'Appointment Time', 'Status', 'Reason', 'Notes'
        ];

        $rows = $appointments->map(function ($appointment) {
            return [
                $appointment->id,
                $appointment->patient->full_name,
                $appointment->doctor ? $appointment->doctor->user->name : 'N/A',
                $appointment->appointment_date->format('Y-m-d'),
                $appointment->appointment_time,
                $appointment->status,
                $appointment->reason,
                $appointment->notes ?? 'N/A'
            ];
        })->toArray();

        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * Patient Quick Search with Auto-suggest
     */
    public function searchPatients(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where(function ($q) use ($query) {
            $q->where('full_name', 'LIKE', "%{$query}%")
              ->orWhere('patient_id', 'LIKE', "%{$query}%")
              ->orWhere('phone', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        })
        ->select('id', 'patient_id', 'full_name', 'phone', 'email', 'date_of_birth', 'gender')
        ->limit(10)
        ->get()
        ->map(function ($patient) {
            return [
                'id' => $patient->id,
                'patient_id' => $patient->patient_id,
                'full_name' => $patient->full_name,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'age' => $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age : null,
                'gender' => $patient->gender,
                'display_text' => "{$patient->full_name} ({$patient->patient_id}) - {$patient->phone}",
                'url' => route('patients.show', $patient->id)
            ];
        });

        return response()->json($patients);
    }

    /**
     * Advanced Patient Search
     */
    public function advancedPatientSearch(Request $request)
    {
        $query = Patient::query();

        // Search by name
        if ($request->filled('name')) {
            $query->where('full_name', 'LIKE', "%{$request->name}%");
        }

        // Search by patient ID
        if ($request->filled('patient_id')) {
            $query->where('patient_id', 'LIKE', "%{$request->patient_id}%");
        }

        // Search by phone
        if ($request->filled('phone')) {
            $query->where('phone', 'LIKE', "%{$request->phone}%");
        }

        // Search by email
        if ($request->filled('email')) {
            $query->where('email', 'LIKE', "%{$request->email}%");
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by age range
        if ($request->filled('age_from')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= ?', [$request->age_from]);
        }

        if ($request->filled('age_to')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= ?', [$request->age_to]);
        }

        // Filter by verification status
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified === 'true');
        }

        $patients = $query->with(['appointments' => function ($q) {
            $q->latest()->limit(3);
        }])
        ->orderBy('full_name')
        ->paginate(20);

        return view('patients.search-results', compact('patients'));
    }
}
