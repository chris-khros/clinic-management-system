<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receptionist Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg mb-8">
                <div class="p-6 text-white">
                    <h1 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="text-blue-100">Here's what's happening at the clinic today.</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today's Appointments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Today's Appointments</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $data['today_appointments'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Patients -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Patients</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $data['total_patients'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Appointments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pending Appointments</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $data['pending_appointments'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Doctors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Available Doctors</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $data['total_doctors'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Today's Appointments List -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Today's Appointments</h3>
                                <a href="{{ route('appointments.calendar') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Calendar
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($data['todays_appointments_list']->count() > 0)
                                <div class="space-y-4">
                                    @foreach($data['todays_appointments_list'] as $appointment)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                                <span class="text-blue-600 font-semibold text-sm">
                                                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $appointment->patient->full_name }}
                                                            </p>
                                                            <p class="text-sm text-gray-500">
                                                                Dr. {{ $appointment->doctor->user->name ?? 'N/A' }}
                                                            </p>
                                                            @if($appointment->reason)
                                                                <p class="text-xs text-gray-400 mt-1">
                                                                    {{ \Illuminate\Support\Str::limit($appointment->reason, 50) }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($appointment->status === 'scheduled') bg-blue-100 text-blue-800
                                                        @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                        @elseif($appointment->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                        @elseif($appointment->status === 'completed') bg-gray-100 text-gray-800
                                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ ucfirst($appointment->status) }}
                                                    </span>
                                                    <a href="{{ route('appointments.show', $appointment) }}" 
                                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                        View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments today</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by scheduling a new appointment.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Schedule Appointment
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Patient Search -->
                <div class="space-y-6">
                    <!-- Patient Quick Search -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Patient Search</h3>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <input type="text"
                                       id="receptionist-patient-search"
                                       placeholder="Search by name, ID, phone, or email..."
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       autocomplete="off">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Search Results -->
                            <div id="receptionist-search-results" class="mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-96 overflow-y-auto">
                                <!-- Results will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <a href="{{ route('patients.create') }}" class="block w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Register New Patient</div>
                                            <div class="text-xs text-gray-500">Add patient to system</div>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('appointments.create') }}" class="block w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Schedule Appointment</div>
                                            <div class="text-xs text-gray-500">Book new appointment</div>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('appointments.calendar') }}" class="block w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">View Calendar</div>
                                            <div class="text-xs text-gray-500">See all appointments</div>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('patients.index') }}" class="block w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Manage Patients</div>
                                            <div class="text-xs text-gray-500">View all patients</div>
                                        </div>
                                    </div>
                                </a>

                                <a href="{{ route('billing.index') }}" class="block w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Billing</div>
                                            <div class="text-xs text-gray-500">Manage bills & payments</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Notifications -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Appointments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Appointments</h3>
                            <a href="{{ route('appointments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $recentAppointments = \App\Models\Appointment::with(['patient', 'doctor.user'])
                                ->latest()
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @if($recentAppointments->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentAppointments as $appointment)
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $appointment->patient->full_name }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $appointment->appointment_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($appointment->status === 'scheduled') bg-blue-100 text-blue-800
                                                @elseif($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($appointment->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                @elseif($appointment->status === 'completed') bg-gray-100 text-gray-800
                                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">No recent appointments</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">Patient Registration</span>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">Appointment Scheduling</span>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">Billing System</span>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">Email Notifications</span>
                                </div>
                                <span class="text-sm text-green-600">Active</span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Last updated: {{ now()->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Patient Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            initializeReceptionistSearch();
        });

        function initializeReceptionistSearch() {
            const searchInput = document.getElementById('receptionist-patient-search');
            const searchResults = document.getElementById('receptionist-search-results');
            let searchTimeout;

            // Debounced search function
            function performReceptionistSearch(query) {
                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                fetch(`/search/patients?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displayReceptionistSearchResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }

            // Display search results
            function displayReceptionistSearchResults(patients) {
                if (patients.length === 0) {
                    searchResults.innerHTML = '<div class="p-4 text-gray-500 text-center">No patients found</div>';
                } else {
                    searchResults.innerHTML = patients.map(patient => `
                        <a href="${patient.url}" class="block p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">${patient.full_name}</div>
                                    <div class="text-sm text-gray-500">ID: ${patient.patient_id} • ${patient.phone}</div>
                                    ${patient.email ? `<div class="text-sm text-gray-500">${patient.email}</div>` : ''}
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">${patient.gender}</div>
                                    ${patient.age ? `<div class="text-sm text-gray-500">${patient.age} years</div>` : ''}
                                </div>
                            </div>
                        </a>
                    `).join('');
                }
                searchResults.classList.remove('hidden');
            }

            // Event listeners
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performReceptionistSearch(query);
                }, 300);
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Handle keyboard navigation
            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    searchResults.classList.add('hidden');
                    searchInput.blur();
                }
            });
        }
    </script>
</x-app-layout>
