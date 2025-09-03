<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Patient Details') }}
            </h2>
            <div class="flex space-x-2">
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
                            <img src="{{ $patient->photo ? asset('storage/' . $patient->photo) : asset('images/default-avatar.png') }}"
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

            <!-- Tabs Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button onclick="showTab('appointments')" id="appointments-tab" class="tab-button border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Appointments ({{ $patient->appointments->count() }})
                            </button>
                            <button onclick="showTab('consultations')" id="consultations-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Consultations ({{ $patient->consultations->count() }})
                            </button>
                            <button onclick="showTab('billing')" id="billing-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Billing ({{ $patient->bills->count() }})
                            </button>
                        </nav>
                    </div>

                    <!-- Appointments Tab -->
                    <div id="appointments-content" class="tab-content">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Appointments</h4>
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
                                                {{ $appointment->doctor->name ?? 'N/A' }}
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

                    <!-- Consultations Tab -->
                    <div id="consultations-content" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Consultations</h4>
                            <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                New Consultation
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
                                                {{ $consultation->doctor->name ?? 'N/A' }}
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

                    <!-- Billing Tab -->
                    <div id="billing-content" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Billing History</h4>
                            <a href="{{ route('billing.create', ['patient_id' => $patient->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Create Bill
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($patient->bills as $bill)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($bill->bill_date)->format('M j, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($bill->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($bill->status === 'paid') bg-green-100 text-green-800
                                                    @elseif($bill->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($bill->status === 'overdue') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($bill->status) }}
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
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active styles from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Add active styles to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600');
        }

        // Initialize with appointments tab active
        document.addEventListener('DOMContentLoaded', function() {
            showTab('appointments');
        });
    </script>
    @endpush
</x-app-layout>
