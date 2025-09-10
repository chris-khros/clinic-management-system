<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Staff Management
    Route::resource('staff', StaffController::class);
    Route::patch('/staff/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.toggle-status');

    // Patient Management
    Route::post('patients/verify-all', [PatientController::class, 'verifyAll'])->name('patients.verify-all');
    Route::resource('patients', PatientController::class);
    Route::get('patients/{patient}/verify-otp', [PatientController::class, 'showOtpForm'])->name('patients.otp.form');
    Route::post('patients/{patient}/verify-otp', [PatientController::class, 'verifyOtp'])->name('patients.otp.verify');
    Route::post('patients/{patient}/documents', [PatientController::class, 'uploadDocument'])->name('patients.documents.upload');
    Route::patch('/patients/{patient}/verify', [PatientController::class, 'verify'])->name('patients.verify');
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

    // Appointment Management
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/doctor-schedule', [AppointmentController::class, 'getDoctorSchedule'])->name('appointments.doctor-schedule');
    Route::post('/appointments/lock', [AppointmentController::class, 'lock'])->name('appointments.lock');
    Route::post('/appointments/unlock', [AppointmentController::class, 'unlock'])->name('appointments.unlock');
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/google-calendar', [AppointmentController::class, 'addEventToGoogleCalendar'])->name('appointments.google-calendar');
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');

    // Consultation Management
    Route::resource('consultations', ConsultationController::class);
    Route::get('/consultations/patient/{patient}/history', [ConsultationController::class, 'patientHistory'])->name('consultations.patient-history');
    Route::patch('/consultations/{consultation}/complete', [ConsultationController::class, 'complete'])->name('consultations.complete');
    Route::patch('/consultations/{consultation}/follow-up', [ConsultationController::class, 'followUp'])->name('consultations.follow-up');
    Route::get('/medical-records/{medicalRecord}/download', [ConsultationController::class, 'downloadMedicalRecord'])->name('medical-records.download');

    // Billing Management
    Route::resource('billing', BillingController::class)->parameters(['billing' => 'bill']);
    Route::patch('/billing/{bill}/mark-paid', [BillingController::class, 'markAsPaid'])->name('billing.mark-paid');
    Route::patch('/billing/{bill}/mark-partial', [BillingController::class, 'markAsPartial'])->name('billing.mark-partial');
    Route::patch('/billing/{bill}/status', [BillingController::class, 'updateStatus'])->name('billing.update-status');
    Route::get('/billing/{bill}/invoice', [BillingController::class, 'generateInvoice'])->name('billing.invoice');
    Route::get('/billing/reports', [BillingController::class, 'reports'])->name('billing.reports');

    // Announcement Management
    Route::resource('announcements', AnnouncementController::class);
    Route::patch('/announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle-status');
    Route::get('/announcements/public', [AnnouncementController::class, 'publicIndex'])->name('announcements.public');

    // Reports and Analytics (integrated into dashboard)
    Route::get('/dashboard/income-summary', [DashboardController::class, 'incomeSummary'])->name('dashboard.income-summary');
    Route::get('/dashboard/patient-flow', [DashboardController::class, 'patientFlow'])->name('dashboard.patient-flow');
    Route::get('/dashboard/export/income-summary', [DashboardController::class, 'exportIncomeSummary'])->name('dashboard.export.income-summary');
    Route::get('/dashboard/export/patient-flow', [DashboardController::class, 'exportPatientFlow'])->name('dashboard.export.patient-flow');
    Route::post('/dashboard/email-report', [DashboardController::class, 'emailReport'])->name('dashboard.email-report');

    // Patient Quick Search
    Route::get('/search/patients', [DashboardController::class, 'searchPatients'])->name('search.patients');
    Route::get('/search/patients/advanced', [DashboardController::class, 'advancedPatientSearch'])->name('search.patients.advanced');

    // Receptionist-specific routes
    Route::middleware(['auth', 'role:receptionist'])->group(function () {
        Route::get('/receptionist', [ReceptionistController::class, 'index'])->name('receptionist.dashboard');
        Route::get('/receptionist/todays-appointments', [ReceptionistController::class, 'getTodaysAppointments'])->name('receptionist.todays-appointments');
        Route::get('/receptionist/upcoming-appointments', [ReceptionistController::class, 'getUpcomingAppointments'])->name('receptionist.upcoming-appointments');
        Route::get('/receptionist/appointment-stats', [ReceptionistController::class, 'getAppointmentStats'])->name('receptionist.appointment-stats');
        Route::get('/receptionist/patient-stats', [ReceptionistController::class, 'getPatientStats'])->name('receptionist.patient-stats');
        Route::get('/receptionist/billing-stats', [ReceptionistController::class, 'getBillingStats'])->name('receptionist.billing-stats');
        Route::get('/receptionist/doctor-availability', [ReceptionistController::class, 'getDoctorAvailability'])->name('receptionist.doctor-availability');
        Route::get('/receptionist/quick-search', [ReceptionistController::class, 'quickPatientSearch'])->name('receptionist.quick-search');
        Route::get('/receptionist/dashboard-summary', [ReceptionistController::class, 'getDashboardSummary'])->name('receptionist.dashboard-summary');
        Route::get('/receptionist/recent-activity', [ReceptionistController::class, 'getRecentActivity'])->name('receptionist.recent-activity');
    });

});

// OTP Management (outside auth middleware for patient verification)
Route::get('/patient/verify-email', [OtpController::class, 'showVerificationForm'])->name('otp.verify-form');
Route::post('/otp/send', [OtpController::class, 'sendOtp'])->name('otp.send');
Route::post('/otp/verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp/resend', [OtpController::class, 'resendOtp'])->name('otp.resend');

// OTP Management (inside auth middleware for admin functions)
Route::middleware('auth')->group(function () {
    Route::post('/patients/{patient}/send-otp', [OtpController::class, 'sendOtpForPatient'])->name('patients.send-otp');
});


require __DIR__.'/auth.php';
