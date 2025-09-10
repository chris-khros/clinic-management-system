<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Patient Quick Search & Filter -->
                    <form id="appointments-filter-form" method="GET" action="{{ route('appointments.index') }}" class="mb-6">
                        <input type="hidden" id="appointments_patient_id" name="patient_id" value="{{ request('patient_id') }}">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Patient Quick Search</h3>
                        <div class="relative">
                            <input type="text"
                                   id="appointments-patient-search"
                                   placeholder="Search patient by name, ID, phone, or email..."
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div id="appointments-search-results" class="mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-96 overflow-y-auto"></div>
                    </form>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Appointment Management</h3>
                        <div class="space-x-2">
                            <a href="{{ route('appointments.calendar') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded border">Calendar</a>
                            <a href="{{ route('appointments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Schedule Appointment</a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ $appointment->patient->full_name ?? ('#'.$appointment->patient_id) }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ $appointment->doctor->user->name ?? ('Doctor #'.$appointment->doctor_id) }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($appointment->status)
                                                    @case('scheduled') bg-blue-100 text-blue-800 @break
                                                    @case('confirmed') bg-green-100 text-green-800 @break
                                                    @case('in_progress') bg-yellow-100 text-yellow-800 @break
                                                    @case('completed') bg-gray-100 text-gray-800 @break
                                                    @case('cancelled') bg-red-100 text-red-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch
                                            ">{{ ucfirst(str_replace('_',' ', $appointment->status)) }}</span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('appointments.show', $appointment) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <a href="{{ route('appointments.edit', $appointment) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                            @if(in_array($appointment->status, ['scheduled','confirmed']))
                                                <a href="{{ route('consultations.create', ['appointment' => $appointment->id]) }}" class="text-green-600 hover:text-green-800">Consult</a>
                                            @endif
                                            @if($appointment->status !== 'cancelled')
                                                <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="cancellation_reason" value="Cancelled via list" />
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Cancel this appointment?')">Cancel</button>
                                                </form>
                                            @endif
                                            @if($appointment->status === 'scheduled')
                                                <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-700 hover:text-green-900">Confirm</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">No appointments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function initAppointmentsSearch(){
            const input = document.getElementById('appointments-patient-search');
            const results = document.getElementById('appointments-search-results');
            if (!input || !results) return;

            let t = null;
            function render(items){
                if (!items || items.length === 0){
                    results.innerHTML = '<div class="p-4 text-gray-500 text-center">No patients found</div>';
                } else {
                    results.innerHTML = items.map(p => `
                        <button type="button" data-id="${p.id}" class="w-full text-left p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">${p.full_name}</div>
                                    <div class="text-sm text-gray-500">ID: ${p.patient_id} • ${p.phone ?? ''}</div>
                                    ${p.email ? `<div class=\"text-sm text-gray-500\">${p.email}</div>` : ''}
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">${p.gender ?? ''}</div>
                                    ${p.age ? `<div class=\"text-sm text-gray-500\">${p.age} years</div>` : ''}
                                </div>
                            </div>
                        </button>
                    `).join('');
                }
                results.classList.remove('hidden');
            }

            function search(q){
                if (!q || q.trim().length < 2){ results.classList.add('hidden'); return; }
                fetch(`/search/patients?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(render)
                    .catch(() => { results.innerHTML = '<div class="p-4 text-red-600 text-center">Search error</div>'; results.classList.remove('hidden'); });
            }

            input.addEventListener('input', function(){
                clearTimeout(t);
                const q = this.value;
                t = setTimeout(() => search(q), 300);
            });

            document.addEventListener('click', function(e){
                if (!input.contains(e.target) && !results.contains(e.target)) results.classList.add('hidden');
            });
            input.addEventListener('keydown', function(e){ if (e.key === 'Escape') { results.classList.add('hidden'); input.blur(); } });

            // Choose patient -> set hidden filter and submit
            results.addEventListener('click', function(e){
                const btn = e.target.closest('button[data-id]');
                if (!btn) return;
                document.getElementById('appointments_patient_id').value = btn.getAttribute('data-id');
                document.getElementById('appointments-filter-form').submit();
            });
        })();
    </script>
</x-app-layout>
