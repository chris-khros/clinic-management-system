<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AnnouncementController;
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
    Route::resource('patients', PatientController::class);
    Route::patch('/patients/{patient}/verify', [PatientController::class, 'verify'])->name('patients.verify');
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

    // Appointment Management
    Route::resource('appointments', AppointmentController::class);
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/doctor-schedule', [AppointmentController::class, 'getDoctorSchedule'])->name('appointments.doctor-schedule');

    // Consultation Management
    Route::resource('consultations', ConsultationController::class);
    Route::get('/consultations/patient/{patient}/history', [ConsultationController::class, 'patientHistory'])->name('consultations.patient-history');
    Route::patch('/consultations/{consultation}/complete', [ConsultationController::class, 'complete'])->name('consultations.complete');
    Route::patch('/consultations/{consultation}/follow-up', [ConsultationController::class, 'followUp'])->name('consultations.follow-up');

    // Billing Management
    Route::resource('billing', BillingController::class);
    Route::patch('/billing/{bill}/mark-paid', [BillingController::class, 'markAsPaid'])->name('billing.mark-paid');
    Route::patch('/billing/{bill}/mark-partial', [BillingController::class, 'markAsPartial'])->name('billing.mark-partial');
    Route::get('/billing/{bill}/invoice', [BillingController::class, 'generateInvoice'])->name('billing.invoice');
    Route::get('/billing/reports', [BillingController::class, 'reports'])->name('billing.reports');

    // Announcement Management
    Route::resource('announcements', AnnouncementController::class);
    Route::patch('/announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle-status');
    Route::get('/announcements/public', [AnnouncementController::class, 'publicIndex'])->name('announcements.public');
});

require __DIR__.'/auth.php';
