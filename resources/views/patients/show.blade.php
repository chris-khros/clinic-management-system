<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Patient Details') }}
            </h2>
            <div class="flex space-x-2">
                @if (!$patient->is_verified)
                    <a href="{{ route('otp.verify-form', ['email' => $patient->email]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Verify OTP
                    </a>
                    <button onclick="sendOtp({{ $patient->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Send OTP
                    </button>
                @endif
                <a href="{{ route('patients.edit', $patient) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit Patient
                </a>
                <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this patient?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Delete Patient
                    </button>
                </form>
                <a href="{{ route('patients.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Patient Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Patient Photo and Basic Info -->
                        <div class="md:col-span-1 flex flex-col items-center">
                            <img src="{{ $patient->photo ? asset('storage/' . $patient->photo) : asset('user-icon.png') }}"
                                 alt="{{ $patient->full_name }}"
                                 class="w-48 h-48 rounded-full object-cover border-4 border-gray-300 mb-4">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $patient->full_name }}</h3>
                            <p class="text-gray-600 text-lg">{{ $patient->patient_id }}</p>
                            <div class="mt-4">
                                @if ($patient->is_verified)
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Verified
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Unverified
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-semibold border-b pb-2 mb-4 text-gray-900">Personal Information</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Date of Birth</p>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($patient->date_of_birth)->format('F j, Y') }}</p>
                                    <p class="text-sm text-gray-600">({{ $patient->age }} years old)</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Gender</p>
                                    <p class="text-gray-900 capitalize">{{ $patient->gender }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Phone Number</p>
                                    <p class="text-gray-900">{{ $patient->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Email Address</p>
                                    <p class="text-gray-900">{{ $patient->email ?? 'Not provided' }}</p>
                                </div>
                                @if($patient->blood_group)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Blood Group</p>
                                    <p class="text-gray-900">{{ $patient->blood_group }}</p>
                                </div>
                                @endif
                                <div class="sm:col-span-2">
                                    <p class="text-sm text-gray-500 font-medium">Address</p>
                                    <p class="text-gray-900">{{ $patient->address ?? 'Not provided' }}</p>
                                </div>
                                @if($patient->emergency_contact_name)
                                <div class="sm:col-span-2">
                                    <p class="text-sm text-gray-500 font-medium">Emergency Contact</p>
                                    <p class="text-gray-900">{{ $patient->emergency_contact_name }} - {{ $patient->emergency_contact_phone }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    @if($patient->medical_history || $patient->allergies || $patient->notes)
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold border-b pb-2 mb-4 text-gray-900">Medical Information</h4>
                        <div class="grid grid-cols-1 gap-4">
                            @if($patient->medical_history)
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Medical History</p>
                                <p class="text-gray-900">{{ $patient->medical_history }}</p>
                            </div>
                            @endif
                            @if($patient->allergies)
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Allergies</p>
                                <p class="text-gray-900">{{ $patient->allergies }}</p>
                            </div>
                            @endif
                            @if($patient->notes)
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Notes</p>
                                <p class="text-gray-900">{{ $patient->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Appointments Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Appointments ({{ $patient->appointments->count() }})</h3>
                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Schedule Appointment
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($patient->appointments as $appointment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M j, Y') }}<br>
                                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $appointment->doctor->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('appointments.show', $appointment) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No appointments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Consultations Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Consultations ({{ $patient->consultations->count() }})</h3>
                        <a href="{{ route('appointments.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Start from Appointment
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($patient->consultations as $consultation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($consultation->consultation_date)->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $consultation->doctor->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ Str::limit($consultation->diagnosis ?? 'No diagnosis recorded', 50) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('consultations.show', $consultation) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No consultations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Billing Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Billing History ({{ $patient->bills->count() }})</h3>
                        <a href="{{ route('billing.create', ['patient_id' => $patient->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Create Bill
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($patient->bills as $bill)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ optional($bill->bill_date ?? $bill->created_at)->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ${{ number_format($bill->total_amount ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php($status = $bill->payment_status ?? 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($status === 'paid') bg-green-100 text-green-800
                                                @elseif($status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('billing.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No billing records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Patient Documents Upload & List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Patient Documents</h3>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('patients.documents.upload', $patient) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-1">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div class="md:col-span-1">
                                <label for="document_type" class="block text-sm font-medium text-gray-700">Type (optional)</label>
                                <select name="document_type" id="document_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Select type...</option>
                                    <option value="Lab Report">Lab Report</option>
                                    <option value="Scan">Scan</option>
                                    <option value="Prescription">Prescription</option>
                                    <option value="Referral">Referral</option>
                                    <option value="Medical Record">Medical Record</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="file" class="block text-sm font-medium text-gray-700">File (PDF, JPG, PNG, max 10MB)</label>
                                <input type="file" name="file" id="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-700" required>
                            </div>
                            <div class="md:col-span-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description (optional)</label>
                                <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                            <div class="md:col-span-4">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Upload Document
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Documents List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($patient->documents as $doc)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">{{ $doc->title }}</div>
                                            <div class="text-gray-500 text-xs">{{ $doc->file_type }} • {{ number_format($doc->file_size / 1024, 1) }} KB</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $doc->document_type ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ optional($doc->uploaded_at)->format('M j, Y g:i A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" download class="text-green-600 hover:text-green-900">Download</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No documents uploaded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sendOtp(patientId) {
            if (confirm('Send OTP to this patient?')) {
                fetch(`/patients/${patientId}/send-otp`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('OTP sent successfully to ' + data.message.split(' to ')[1]);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending OTP');
                });
            }
        }
    </script>

</x-app-layout>
