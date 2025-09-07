<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Email Verification') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center mb-6">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Verify Your Email</h3>
                        <p class="text-sm text-gray-600 mt-2">
                            We've sent a verification code to <strong>{{ $patient->email }}</strong>
                        </p>
    </div>

                    <form id="verify-form">
                        @csrf
                        <input type="hidden" name="email" value="{{ $patient->email }}">

                        <div class="mb-4">
                            <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                                Enter Verification Code
                            </label>
                            <input type="text"
                                   id="otp"
                                   name="otp"
                                   maxlength="6"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-center text-2xl tracking-widest"
                                   placeholder="000000"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Enter the 6-digit code sent to your email</p>
        </div>

                        <div class="mb-4">
                            <button type="submit"
                                    id="verify-btn"
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="verify-text">Verify Email</span>
                                <svg id="verify-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Didn't receive the code?
                                <button type="button"
                                        id="resend-btn"
                                        class="text-blue-600 hover:text-blue-500 font-medium">
                                    Resend Code
                                </button>
                            </p>
                            <p id="resend-timer" class="text-xs text-gray-500 mt-1 hidden">
                                You can resend code in <span id="countdown">60</span> seconds
                            </p>
            </div>
        </form>

                    <div id="success-message" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800" id="success-text">
                                    Email verified successfully!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div id="error-message" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800" id="error-text">
                                    Verification failed. Please try again.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('verify-form');
            const otpInput = document.getElementById('otp');
            const verifyBtn = document.getElementById('verify-btn');
            const verifyText = document.getElementById('verify-text');
            const verifySpinner = document.getElementById('verify-spinner');
            const resendBtn = document.getElementById('resend-btn');
            const resendTimer = document.getElementById('resend-timer');
            const countdown = document.getElementById('countdown');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const successText = document.getElementById('success-text');
            const errorText = document.getElementById('error-text');

            let countdownInterval;
            let canResend = false;

            // Auto-format OTP input
            otpInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 6) {
                    value = value.substring(0, 6);
                }
                e.target.value = value;
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const email = document.querySelector('input[name="email"]').value;
                const otp = otpInput.value;

                if (otp.length !== 6) {
                    showError('Please enter a valid 6-digit code');
                    return;
                }

                setLoading(true);
                hideMessages();

                fetch('{{ route("otp.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        otp: otp
                    })
                })
                .then(response => response.json())
                .then(data => {
                    setLoading(false);

                    if (data.success) {
                        showSuccess(data.message);
                        setTimeout(() => {
                            window.location.href = '{{ route("patients.index") }}';
                        }, 2000);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    setLoading(false);
                    showError('An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            });

            // Resend OTP
            resendBtn.addEventListener('click', function() {
                if (!canResend) return;

                const email = document.querySelector('input[name="email"]').value;

                setResendLoading(true);
                hideMessages();

                fetch('{{ route("otp.resend") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email
                    })
                })
                .then(response => response.json())
                .then(data => {
                    setResendLoading(false);

                    if (data.success) {
                        showSuccess('New verification code sent to your email');
                        startCountdown();
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    setResendLoading(false);
                    showError('Failed to resend code. Please try again.');
                    console.error('Error:', error);
                });
            });

            function setLoading(loading) {
                verifyBtn.disabled = loading;
                if (loading) {
                    verifyText.textContent = 'Verifying...';
                    verifySpinner.classList.remove('hidden');
                } else {
                    verifyText.textContent = 'Verify Email';
                    verifySpinner.classList.add('hidden');
                }
            }

            function setResendLoading(loading) {
                resendBtn.disabled = loading;
                resendBtn.textContent = loading ? 'Sending...' : 'Resend Code';
            }

            function showSuccess(message) {
                successText.textContent = message;
                successMessage.classList.remove('hidden');
                errorMessage.classList.add('hidden');
            }

            function showError(message) {
                errorText.textContent = message;
                errorMessage.classList.remove('hidden');
                successMessage.classList.add('hidden');
            }

            function hideMessages() {
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');
            }

            function startCountdown() {
                canResend = false;
                resendBtn.disabled = true;
                resendTimer.classList.remove('hidden');

                let timeLeft = 60;
                countdown.textContent = timeLeft;

                countdownInterval = setInterval(() => {
                    timeLeft--;
                    countdown.textContent = timeLeft;

                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        canResend = true;
                        resendBtn.disabled = false;
                        resendTimer.classList.add('hidden');
                    }
                }, 1000);
            }

            // Start initial countdown
            startCountdown();
        });
    </script>
</x-app-layout>
