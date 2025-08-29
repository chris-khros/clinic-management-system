<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Bill;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
