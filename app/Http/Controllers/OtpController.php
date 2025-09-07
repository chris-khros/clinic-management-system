<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP for patient verification
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:patients,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = Patient::where('email', $request->email)->first();

        if ($patient->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified'
            ], 400);
        }

        if (!$this->otpService->canRequestNewOtp($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new OTP'
            ], 429);
        }

        $sent = $this->otpService->sendVerificationOtp($patient);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.'
        ], 500);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:patients,email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->otpService->verifyOtp($request->email, $request->otp);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'patient' => $result['patient']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:patients,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = Patient::where('email', $request->email)->first();

        if ($patient->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified'
            ], 400);
        }

        if (!$this->otpService->canRequestNewOtp($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new OTP'
            ], 429);
        }

        $sent = $this->otpService->resendOtp($patient);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to resend OTP. Please try again.'
        ], 500);
    }

    /**
     * Show OTP verification form
     */
    public function showVerificationForm(Request $request)
    {
        $email = $request->get('email');

        if (!$email) {
            return redirect()->route('patients.index')->with('error', 'Email address is required');
        }

        $patient = Patient::where('email', $email)->first();

        if (!$patient) {
            return redirect()->route('patients.index')->with('error', 'Patient not found');
        }

        if ($patient->is_verified) {
            return redirect()->route('patients.show', $patient)->with('success', 'Email is already verified');
        }

        return view('auth.verify-email', compact('patient'));
    }

    /**
     * Send OTP for specific patient (from patient show page)
     */
    public function sendOtpForPatient(Patient $patient): JsonResponse
    {
        if ($patient->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Patient is already verified'
            ], 400);
        }

        if (!$this->otpService->canRequestNewOtp($patient->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new OTP'
            ], 429);
        }

        $sent = $this->otpService->sendVerificationOtp($patient);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to ' . $patient->email
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.'
        ], 500);
    }
}
