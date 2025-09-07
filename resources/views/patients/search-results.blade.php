<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Patient Search Results') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
                <a href="{{ route('patients.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Add New Patient
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Search Results</h3>
                    <p class="text-gray-600">
                        Found {{ $patients->total() }} patient(s) matching your search criteria.
                        @if($patients->hasPages())
                            Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} results.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Search Filters Applied -->
            @if(request()->anyFilled(['name', 'patient_id', 'phone', 'email', 'gender', 'is_verified']))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Applied Filters:</h4>
                <div class="flex flex-wrap gap-2">
                    @if(request('name'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Name: {{ request('name') }}
                        </span>
                    @endif
                    @if(request('patient_id'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ID: {{ request('patient_id') }}
                        </span>
                    @endif
                    @if(request('phone'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Phone: {{ request('phone') }}
                        </span>
                    @endif
                    @if(request('email'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Email: {{ request('email') }}
                        </span>
                    @endif
                    @if(request('gender'))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Gender: {{ ucfirst(request('gender')) }}
                        </span>
                    @endif
                    @if(request('is_verified') !== null)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Status: {{ request('is_verified') === 'true' ? 'Verified' : 'Not Verified' }}
                        </span>
                    @endif
                </div>
            </div>
            @endif

            <!-- Patients List -->
            @if($patients->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age/Gender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recent Appointments</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($patients as $patient)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($patient->full_name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $patient->full_name }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">ID: {{ $patient->patient_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $patient->phone }}</div>
                                    @if($patient->email)
                                        <div class="text-sm text-gray-500">{{ $patient->email }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($patient->date_of_birth)
                                            {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ ucfirst($patient->gender) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $patient->is_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $patient->is_verified ? 'Verified' : 'Not Verified' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($patient->appointments->count() > 0)
                                        <div class="text-sm text-gray-900">
                                            {{ $patient->appointments->count() }} appointment(s)
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Latest: {{ $patient->appointments->first()->appointment_date->format('M d, Y') }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">No appointments</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-900">
                                            View
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="text-green-600 hover:text-green-900">
                                            Book
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($patients->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $patients->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
            @else
            <!-- No Results -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No patients found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Try adjusting your search criteria or create a new patient.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('patients.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Patient
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
