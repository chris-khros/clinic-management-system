<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Consultation Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('consultations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">Back to List</a>
                <a href="{{ route('consultations.edit', $consultation) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded">Edit</a>
                <button onclick="printConsultation()" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">Print</button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Patient & Consultation Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img class="h-16 w-16 rounded-full mr-4" src="{{ $consultation->patient->photo ? asset('storage/' . $consultation->patient->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($consultation->patient->first_name . ' ' . $consultation->patient->last_name) . '&color=7F9CF5&background=EBF4FF' }}" alt="Patient Photo">
                            <div>
                                <h3 class="text-xl font-bold">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</h3>
                                <p class="text-sm text-gray-500">Patient ID: {{ $consultation->patient->id }}</p>
                                <p class="text-sm text-gray-500">Consultation Date: {{ $consultation->consultation_date->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Doctor: {{ $consultation->doctor->user->name ?? 'Dr. Unknown' }}</p>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($consultation->status === 'completed') bg-green-100 text-green-800
                                @elseif($consultation->status === 'follow_up') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($consultation->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Patient Info & Quick Actions -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Patient Quick Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900">Patient Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Age</p>
                                    <p class="font-medium">{{ $consultation->patient->age ?? 'N/A' }} years</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Gender</p>
                                    <p class="font-medium capitalize">{{ $consultation->patient->gender ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Blood Group</p>
                                    <p class="font-medium">{{ $consultation->patient->blood_group ?? 'N/A' }}</p>
                                </div>
                                @if($consultation->patient->allergies)
                                <div>
                                    <p class="text-sm text-gray-500">Allergies</p>
                                    <p class="font-medium text-red-600">{{ $consultation->patient->allergies }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('patients.show', $consultation->patient) }}" class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700">
                                    View Patient Profile
                                </a>
                                <a href="{{ route('appointments.show', $consultation->appointment) }}" class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700">
                                    View Appointment
                                </a>
                                @if($consultation->status !== 'completed')
                                <form action="{{ route('consultations.complete', $consultation) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-700">
                                        Mark as Completed
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Consultation Timeline -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900">Consultation Timeline</h3>
                            <div class="space-y-4">
                                @if($consultation->consultation_started_at)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-2 h-2 bg-green-400 rounded-full"></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Consultation Started</p>
                                        <p class="text-xs text-gray-500">{{ $consultation->consultation_started_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                @endif
                                @if($consultation->consultation_completed_at)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-2 h-2 bg-blue-400 rounded-full"></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Consultation Completed</p>
                                        <p class="text-xs text-gray-500">{{ $consultation->consultation_completed_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                @endif
                                @if($consultation->follow_up_date)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-2 h-2 bg-yellow-400 rounded-full"></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Follow-up Scheduled</p>
                                        <p class="text-xs text-gray-500">{{ $consultation->follow_up_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Consultation Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Chief Complaint & Symptoms -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Chief Complaint & Symptoms</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($consultation->chief_complaint)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Chief Complaint</p>
                                    <p class="text-gray-900">{{ $consultation->chief_complaint }}</p>
                                </div>
                                @endif
                                @if($consultation->duration)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Duration</p>
                                    <p class="text-gray-900 capitalize">{{ $consultation->duration }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 font-medium">Detailed Symptoms</p>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->symptoms }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Physical Examination -->
                    @if($consultation->vital_signs || $consultation->physical_findings)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Physical Examination</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($consultation->vital_signs)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Vital Signs</p>
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->vital_signs }}</p>
                                </div>
                                @endif
                                @if($consultation->physical_findings)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Physical Findings</p>
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->physical_findings }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Diagnosis -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Diagnosis</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($consultation->primary_diagnosis)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Primary Diagnosis</p>
                                    <p class="text-gray-900">{{ $consultation->primary_diagnosis }}</p>
                                </div>
                                @endif
                                @if($consultation->secondary_diagnosis)
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Secondary Diagnosis</p>
                                    <p class="text-gray-900">{{ $consultation->secondary_diagnosis }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 font-medium">Detailed Diagnosis</p>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->diagnosis }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Treatment Plan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Treatment Plan</h3>
                            @if($consultation->medications)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 font-medium">Medications Prescribed</p>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->medications }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Treatment Plan</p>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->treatment_plan }}</p>
                            </div>
                            @if($consultation->prescription)
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 font-medium">Prescription Notes</p>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->prescription }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Medical Records & Files -->
                    @if($consultation->medicalRecords->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Records & Files</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($consultation->medicalRecords as $record)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $record->title }}</h4>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($record->record_type === 'prescription') bg-blue-100 text-blue-800
                                            @elseif($record->record_type === 'lab_result') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $record->record_type)) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">{{ $record->description }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-400">{{ $record->file_type }}</span>
                                        <a href="{{ route('medical-records.download', $record) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                            Download
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Notes -->
                    @if($consultation->notes)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $consultation->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function printConsultation() {
            window.print();
        }
    </script>
    @endpush
</x-app-layout>
