<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Bill;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceptionistController extends Controller
{
    /**
     * Display the receptionist dashboard.
     */
    public function index()
    {
        $today = today();
        $tomorrow = $today->copy()->addDay();
        $thisWeek = $today->copy()->startOfWeek();
        $thisMonth = $today->copy()->startOfMonth();

        // Basic statistics
        $data = [
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'total_patients' => Patient::count(),
            'pending_appointments' => Appointment::where('status', 'scheduled')->count(),
            'total_doctors' => Doctor::where('is_active', true)->count(),
            'confirmed_appointments' => Appointment::where('status', 'confirmed')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->count(),
            
            // Today's appointments with detailed information
            'todays_appointments_list' => Appointment::whereDate('appointment_date', $today)
                ->with(['patient', 'doctor.user'])
                ->orderBy('appointment_time')
                ->get(),
            
            // Tomorrow's appointments
            'tomorrows_appointments' => Appointment::whereDate('appointment_date', $tomorrow)
                ->with(['patient', 'doctor.user'])
                ->orderBy('appointment_time')
                ->get(),
            
            // This week's statistics
            'this_week_appointments' => Appointment::whereBetween('appointment_date', [$thisWeek, $thisWeek->copy()->endOfWeek()])->count(),
            
            // This month's statistics
            'this_month_appointments' => Appointment::whereBetween('appointment_date', [$thisMonth, $thisMonth->copy()->endOfMonth()])->count(),
            
            // Recent patients (last 7 days)
            'recent_patients' => Patient::where('created_at', '>=', $today->copy()->subDays(7))->count(),
            
            // Pending bills
            'pending_bills' => Bill::where('payment_status', 'pending')->count(),
            'total_revenue_today' => Bill::whereDate('created_at', $today)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            
            // Upcoming appointments (next 7 days)
            'upcoming_appointments' => Appointment::whereBetween('appointment_date', [$today, $today->copy()->addDays(7)])
                ->whereIn('status', ['scheduled', 'confirmed'])
                ->with(['patient', 'doctor.user'])
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->get(),
            
            // Doctor availability
            'available_doctors' => Doctor::where('is_active', true)
                ->with('user')
                ->get(),
            
            // Appointment status breakdown
            'appointment_status_breakdown' => [
                'scheduled' => Appointment::where('status', 'scheduled')->count(),
                'confirmed' => Appointment::where('status', 'confirmed')->count(),
                'in_progress' => Appointment::where('status', 'in_progress')->count(),
                'completed' => Appointment::where('status', 'completed')->count(),
                'cancelled' => Appointment::where('status', 'cancelled')->count(),
            ],
        ];

        return view('dashboard.receptionist', compact('data'));
    }

    /**
     * Get today's appointments for AJAX requests.
     */
    public function getTodaysAppointments()
    {
        $appointments = Appointment::whereDate('appointment_date', today())
            ->with(['patient', 'doctor.user'])
            ->orderBy('appointment_time')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Get upcoming appointments for the next 7 days.
     */
    public function getUpcomingAppointments()
    {
        $appointments = Appointment::whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->with(['patient', 'doctor.user'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Get appointment statistics for charts.
     */
    public function getAppointmentStats(Request $request)
    {
        $period = $request->get('period', 'week'); // week, month, year
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'week':
                $startDate = today()->startOfWeek();
                $endDate = today()->endOfWeek();
                break;
            case 'month':
                $startDate = today()->startOfMonth();
                $endDate = today()->endOfMonth();
                break;
            case 'year':
                $startDate = today()->startOfYear();
                $endDate = today()->endOfYear();
                break;
        }

        $appointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(appointment_date) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled'),
                DB::raw('SUM(CASE WHEN status = "scheduled" THEN 1 ELSE 0 END) as scheduled')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Get patient statistics.
     */
    public function getPatientStats()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'new_patients_this_month' => Patient::whereMonth('created_at', today()->month)->count(),
            'verified_patients' => Patient::where('is_verified', true)->count(),
            'unverified_patients' => Patient::where('is_verified', false)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get billing statistics.
     */
    public function getBillingStats()
    {
        $today = today();
        
        $stats = [
            'total_revenue_today' => Bill::whereDate('created_at', $today)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'pending_bills' => Bill::where('payment_status', 'pending')->count(),
            'paid_bills_today' => Bill::whereDate('created_at', $today)
                ->where('payment_status', 'paid')
                ->count(),
            'total_revenue_this_month' => Bill::whereMonth('created_at', $today->month)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get doctor availability for a specific date.
     */
    public function getDoctorAvailability(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        
        $doctors = Doctor::where('is_active', true)
            ->with(['user', 'appointments' => function ($query) use ($date) {
                $query->whereDate('appointment_date', $date)
                    ->whereIn('status', ['scheduled', 'confirmed', 'in_progress']);
            }])
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                    'specialization' => $doctor->specialization,
                    'appointments_count' => $doctor->appointments->count(),
                    'max_appointments' => $doctor->max_appointments_per_day,
                    'availability' => $doctor->appointments->count() < $doctor->max_appointments_per_day,
                ];
            });

        return response()->json($doctors);
    }

    /**
     * Quick patient search for autocomplete.
     */
    public function quickPatientSearch(Request $request)
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
                'age' => $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null,
                'gender' => $patient->gender,
                'display_text' => "{$patient->full_name} ({$patient->patient_id}) - {$patient->phone}",
                'url' => route('patients.show', $patient->id)
            ];
        });

        return response()->json($patients);
    }

    /**
     * Get dashboard summary for AJAX refresh.
     */
    public function getDashboardSummary()
    {
        $today = today();
        
        $summary = [
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'pending_appointments' => Appointment::where('status', 'scheduled')->count(),
            'total_patients' => Patient::count(),
            'available_doctors' => Doctor::where('is_active', true)->count(),
            'pending_bills' => Bill::where('payment_status', 'pending')->count(),
            'revenue_today' => Bill::whereDate('created_at', $today)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];

        return response()->json($summary);
    }

    /**
     * Get recent activity for the dashboard.
     */
    public function getRecentActivity()
    {
        $activities = collect();

        // Recent appointments
        $recentAppointments = Appointment::with(['patient', 'doctor.user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'type' => 'appointment',
                    'title' => "Appointment with {$appointment->patient->full_name}",
                    'description' => "Scheduled for {$appointment->appointment_date->format('M d, Y')} at " . Carbon::parse($appointment->appointment_time)->format('H:i'),
                    'status' => $appointment->status,
                    'created_at' => $appointment->created_at,
                ];
            });

        // Recent patients
        $recentPatients = Patient::latest()
            ->limit(3)
            ->get()
            ->map(function ($patient) {
                return [
                    'type' => 'patient',
                    'title' => "New patient registered: {$patient->full_name}",
                    'description' => "Patient ID: {$patient->patient_id}",
                    'status' => $patient->is_verified ? 'verified' : 'pending',
                    'created_at' => $patient->created_at,
                ];
            });

        // Recent bills
        $recentBills = Bill::with('patient')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($bill) {
                return [
                    'type' => 'bill',
                    'title' => "Bill generated for {$bill->patient->full_name}",
                    'description' => "Amount: $" . number_format($bill->total_amount, 2),
                    'status' => $bill->payment_status,
                    'created_at' => $bill->created_at,
                ];
            });

        $activities = $activities
            ->merge($recentAppointments)
            ->merge($recentPatients)
            ->merge($recentBills)
            ->sortByDesc('created_at')
            ->take(10);

        return response()->json($activities->values());
    }
}
