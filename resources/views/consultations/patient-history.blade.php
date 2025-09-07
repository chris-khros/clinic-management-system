<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Patient Consultation History') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('patients.show', $patient) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">Back to Patient</a>
                <a href="{{ route('consultations.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">All Consultations</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Patient Info Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <img class="h-16 w-16 rounded-full mr-4" src="{{ $patient->photo ? asset('storage/' . $patient->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($patient->first_name . ' ' . $patient->last_name) . '&color=7F9CF5&background=EBF4FF' }}" alt="Patient Photo">
                        <div>
                            <h3 class="text-xl font-bold">{{ $patient->first_name }} {{ $patient->last_name }}</h3>
                            <p class="text-sm text-gray-500">Patient ID: {{ $patient->id }}</p>
                            <p class="text-sm text-gray-500">Total Consultations: {{ $consultations->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consultation Timeline -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Consultation Timeline</h3>

                    @forelse($consultations as $consultation)
                    <div class="border-l-4 border-gray-200 pl-6 pb-8 relative">
                        <!-- Timeline dot -->
                        <div class="absolute -left-2 top-0 w-4 h-4 bg-indigo-500 rounded-full"></div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{ $consultation->consultation_date->format('M d, Y') }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $consultation->consultation_date->format('h:i A') }} -
                                        Dr. {{ $consultation->doctor->user->name ?? 'Unknown' }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($consultation->status === 'completed') bg-green-100 text-green-800
                                        @elseif($consultation->status === 'follow_up') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($consultation->status) }}
                                    </span>
                                    <a href="{{ route('consultations.show', $consultation) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View Details
                                    </a>
                                </div>
                            </div>

                            <!-- Chief Complaint -->
                            @if($consultation->chief_complaint)
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700">Chief Complaint:</p>
                                <p class="text-gray-900">{{ $consultation->chief_complaint }}</p>
                            </div>
                            @endif

                            <!-- Primary Diagnosis -->
                            @if($consultation->primary_diagnosis)
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700">Primary Diagnosis:</p>
                                <p class="text-gray-900">{{ $consultation->primary_diagnosis }}</p>
                            </div>
                            @endif

                            <!-- Diagnosis Summary -->
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700">Diagnosis:</p>
                                <p class="text-gray-900">{{ Str::limit($consultation->diagnosis, 150) }}</p>
                            </div>

                            <!-- Treatment Summary -->
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700">Treatment:</p>
                                <p class="text-gray-900">{{ Str::limit($consultation->treatment_plan, 150) }}</p>
                            </div>

                            <!-- Medications -->
                            @if($consultation->medications)
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700">Medications:</p>
                                <p class="text-gray-900">{{ Str::limit($consultation->medications, 100) }}</p>
                            </div>
                            @endif

                            <!-- Follow-up Date -->
                            @if($consultation->follow_up_date)
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700">Follow-up Date:</p>
                                <p class="text-gray-900">{{ $consultation->follow_up_date->format('M d, Y') }}</p>
                            </div>
                            @endif

                            <!-- Medical Records Count -->
                            @if($consultation->medicalRecords->count() > 0)
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700">Attachments:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($consultation->medicalRecords as $record)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($record->record_type === 'prescription') bg-blue-100 text-blue-800
                                        @elseif($record->record_type === 'lab_result') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $record->record_type)) }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Quick Actions -->
                            <div class="flex items-center space-x-2 pt-3 border-t border-gray-200">
                                <a href="{{ route('consultations.show', $consultation) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    View Full Details
                                </a>
                                @if($consultation->medicalRecords->count() > 0)
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('consultations.show', $consultation) }}#medical-records" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    View Files ({{ $consultation->medicalRecords->count() }})
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No consultations found</h3>
                        <p class="mt-1 text-sm text-gray-500">This patient has no consultation history yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Summary Statistics -->
            @if($consultations->count() > 0)
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Consultations</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $consultations->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Completed</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $consultations->where('status', 'completed')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Follow-up Required</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $consultations->where('status', 'follow_up')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
