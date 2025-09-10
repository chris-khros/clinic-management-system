<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Appointment Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('appointments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">Back to List</a>
                <a href="{{ route('appointments.calendar') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded border">Calendar</a>
                <a href="{{ route('appointments.edit', $appointment) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Appointment Overview -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Overview</h3>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($appointment->status)
                                        @case('scheduled') bg-blue-100 text-blue-800 @break
                                        @case('confirmed') bg-green-100 text-green-800 @break
                                        @case('in_progress') bg-yellow-100 text-yellow-800 @break
                                        @case('completed') bg-gray-100 text-gray-800 @break
                                        @case('cancelled') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst(str_replace('_',' ', $appointment->status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Date</p>
                                    <p class="text-gray-900 font-medium">{{ optional($appointment->appointment_date)->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Time</p>
                                    <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Reason</p>
                                    <p class="text-gray-900">{{ $appointment->reason }}</p>
                                </div>
                                @if($appointment->notes)
                                <div>
                                    <p class="text-sm text-gray-500">Notes</p>
                                    <p class="text-gray-900">{{ $appointment->notes }}</p>
                                </div>
                                @endif
                                @if($appointment->confirmed_at)
                                <div>
                                    <p class="text-sm text-gray-500">Confirmed At</p>
                                    <p class="text-gray-900">{{ optional($appointment->confirmed_at)->format('M d, Y h:i A') }}</p>
                                </div>
                                @endif
                                @if($appointment->status === 'cancelled')
                                <div class="sm:col-span-2">
                                    <p class="text-sm text-gray-500">Cancellation</p>
                                    <p class="text-gray-900">{{ optional($appointment->cancelled_at)->format('M d, Y h:i A') }} - {{ $appointment->cancellation_reason ?? 'N/A' }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Consultation section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Consultation</h3>
                                @if(!$appointment->consultation && in_array($appointment->status, ['scheduled','confirmed']))
                                    <a href="{{ route('consultations.create', ['appointment' => $appointment->id]) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded">Start Consultation</a>
                                @endif
                            </div>

                            @if($appointment->consultation)
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-gray-900 font-medium">Consultation recorded</p>
                                        <p class="text-sm text-gray-500">Date: {{ optional($appointment->consultation->consultation_date)->format('M d, Y') }}</p>
                                    </div>
                                    <a href="{{ route('consultations.show', $appointment->consultation) }}" class="text-indigo-600 hover:text-indigo-900">View Consultation</a>
                                </div>
                            @else
                                <p class="text-gray-500">No consultation recorded yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Patient and Doctor Cards + Actions -->
                <div class="space-y-6">
                    <!-- Patient Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Patient</h4>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <p class="text-gray-900 font-medium">{{ $appointment->patient->full_name ?? ('#'.$appointment->patient_id) }}</p>
                                    <p class="text-sm text-gray-500">ID: {{ $appointment->patient->id }}</p>
                                </div>
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Doctor</h4>
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <p class="text-gray-900 font-medium">{{ $appointment->doctor->user->name ?? ('Doctor #'.$appointment->doctor_id) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-3">
                            <h4 class="text-md font-semibold text-gray-900">Actions</h4>

                            <form action="{{ route('appointments.update-status', $appointment) }}" method="POST" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <label class="block text-sm text-gray-700">Change Status</label>
                                <select name="status" id="status_select" class="w-full border rounded p-2" required>
                                    @php($statuses = ['scheduled','confirmed','in_progress','completed','cancelled','no_show','rescheduled'])
                                    @foreach($statuses as $s)
                                        <option value="{{ $s }}" {{ $appointment->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
                                    @endforeach
                                </select>
                                <div id="cancel_reason_wrap" class="hidden">
                                    <label class="block text-sm text-gray-700">Cancellation Reason</label>
                                    <textarea name="cancellation_reason" rows="2" class="w-full border rounded p-2" placeholder="Enter reason"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded">Update Status</button>
                            </form>

                            @if($appointment->status === 'scheduled')
                                <form action="{{ route('appointments.confirm', $appointment) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">Confirm</button>
                                </form>
                            @endif


                            @if($appointment->status !== 'cancelled')
                                <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" onsubmit="return confirm('Cancel this appointment?');" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="block text-sm text-gray-700">Cancellation Reason</label>
                                    <textarea name="cancellation_reason" rows="2" class="w-full border rounded p-2" placeholder="Enter reason" required></textarea>
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded">Cancel Appointment</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function(){
            const select = document.getElementById('status_select');
            const wrap = document.getElementById('cancel_reason_wrap');
            function toggleReason(){
                if (!select) return;
                wrap.classList.toggle('hidden', select.value !== 'cancelled');
            }
            if (select && wrap) {
                select.addEventListener('change', toggleReason);
                toggleReason();
            }
        })();
    </script>
</x-app-layout>


