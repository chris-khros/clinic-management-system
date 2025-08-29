<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-2xl font-bold text-blue-600">{{ $data['total_staff'] }}</div>
                        <div class="text-gray-600">Total Staff</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-2xl font-bold text-green-600">{{ $data['total_patients'] }}</div>
                        <div class="text-gray-600">Total Patients</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-2xl font-bold text-yellow-600">{{ $data['today_appointments'] }}</div>
                        <div class="text-gray-600">Today's Appointments</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-2xl font-bold text-purple-600">${{ number_format($data['total_revenue'], 2) }}</div>
                        <div class="text-gray-600">Total Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('staff.create') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="text-blue-600 font-semibold">Add Staff Member</div>
                            <div class="text-sm text-gray-600">Register new staff member</div>
                        </a>
                        
                        <a href="{{ route('patients.create') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="text-green-600 font-semibold">Register Patient</div>
                            <div class="text-sm text-gray-600">Add new patient</div>
                        </a>
                        
                        <a href="{{ route('appointments.create') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="text-yellow-600 font-semibold">Schedule Appointment</div>
                            <div class="text-sm text-gray-600">Book new appointment</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
