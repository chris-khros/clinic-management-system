<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\Patient;
use App\Mail\OtpVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Send OTP to patient email for verification
     */
    public function sendVerificationOtp(Patient $patient): bool
    {
        try {
            // Create OTP record
            $otp = Otp::createForEmail($patient->email, 'verification');

            // Update patient record with OTP info
            $patient->update([
                'otp' => $otp->otp_code,
                'otp_expires_at' => $otp->expires_at,
            ]);

            // Send email
            Mail::to($patient->email)->send(new OtpVerificationMail($otp->otp_code, $patient));

            Log::info("OTP sent to patient {$patient->email} for verification");

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send OTP to patient {$patient->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify OTP code for patient
     */
    public function verifyOtp(string $email, string $code): array
    {
        try {
            // Verify OTP
            $isValid = Otp::verify($email, $code, 'verification');

            if (!$isValid) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired OTP code'
                ];
            }

            // Find and verify patient
            $patient = Patient::where('email', $email)->first();

            if (!$patient) {
                return [
                    'success' => false,
                    'message' => 'Patient not found'
                ];
            }

            // Mark patient as verified
            $patient->update([
                'is_verified' => true,
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            Log::info("Patient {$patient->email} verified successfully");

            return [
                'success' => true,
                'message' => 'Email verified successfully',
                'patient' => $patient
            ];

        } catch (\Exception $e) {
            Log::error("OTP verification failed for {$email}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Verification failed. Please try again.'
            ];
        }
    }

    /**
     * Resend OTP to patient
     */
    public function resendOtp(Patient $patient): bool
    {
        return $this->sendVerificationOtp($patient);
    }

    /**
     * Check if patient can request new OTP (rate limiting)
     */
    public function canRequestNewOtp(string $email): bool
    {
        $lastOtp = Otp::where('email', $email)
            ->where('type', 'verification')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastOtp) {
            return true;
        }

        // Allow new OTP request after 1 minute
        return $lastOtp->created_at->addMinute()->isPast();
    }

    /**
     * Clean up expired OTPs
     */
    public function cleanupExpiredOtps(): int
    {
        return Otp::where('expires_at', '<', now())
            ->where('is_used', false)
            ->update(['is_used' => true]);
    }
}
