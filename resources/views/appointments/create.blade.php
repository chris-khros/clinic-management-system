<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schedule Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('appointments.store') }}" method="POST" id="appointment-form" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium mb-1">Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="border rounded p-2 w-full" required>
                                <option value="">Select a doctor</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->user->name ?? ('Doctor #'.$doc->id) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Patient</label>
                            <input type="hidden" name="patient_id" id="patient_id" required>
                            <div class="relative">
                                <input type="text"
                                       id="patient_search_input"
                                       placeholder="Search by name, ID, phone, or email..."
                                       class="w-full pl-10 pr-10 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       autocomplete="off">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <button type="button" id="patient_clear_btn" class="absolute inset-y-0 right-0 pr-3 text-gray-400 hover:text-gray-600 hidden" aria-label="Clear">
                                    ✕
                                </button>
                            </div>
                            <div id="patient_search_results" class="mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-72 overflow-y-auto"></div>
                            <p id="patient_selected_hint" class="text-sm text-gray-600 mt-2 hidden"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Date</label>
                                <input type="date" name="appointment_date" id="appointment_date" class="border rounded p-2 w-full" min="{{ now()->addDay()->toDateString() }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Time</label>
                                <select name="appointment_time" id="appointment_time" class="border rounded p-2 w-full" required>
                                    <option value="">Select time</option>
                                </select>
                                <small class="text-gray-500">Only available and unlocked slots are listed.</small>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Reason</label>
                            <textarea name="reason" rows="3" class="border rounded p-2 w-full" required></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Notes (optional)</label>
                            <textarea name="notes" rows="2" class="border rounded p-2 w-full"></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Schedule</button>
                        </div>
                    </form>

                    <script>
                        const doctorEl = document.getElementById('doctor_id');
                        const dateEl = document.getElementById('appointment_date');
                        const timeEl = document.getElementById('appointment_time');
                        const patientIdEl = document.getElementById('patient_id');
                        const patientSearchEl = document.getElementById('patient_search_input');
                        const patientResultsEl = document.getElementById('patient_search_results');
                        const patientClearBtn = document.getElementById('patient_clear_btn');
                        const patientHint = document.getElementById('patient_selected_hint');

                        function timeRange(start, end, stepMinutes = 30) {
                            const out = [];
                            const [sh, sm] = start.split(':').map(Number);
                            const [eh, em] = end.split(':').map(Number);
                            let d = new Date();
                            d.setHours(sh, sm, 0, 0);
                            const endD = new Date();
                            endD.setHours(eh, em, 0, 0);
                            while (d <= endD) {
                                out.push(d.toTimeString().slice(0,5));
                                d = new Date(d.getTime() + stepMinutes*60000);
                            }
                            return out;
                        }

                        async function refreshTimes() {
                            timeEl.innerHTML = '<option>Loading...</option>';
                            const docId = doctorEl.value;
                            const date = dateEl.value;
                            if (!docId || !date) { timeEl.innerHTML = '<option value="">Select time</option>'; return; }

                            const res = await fetch(`{{ route('appointments.doctor-schedule') }}?doctor_id=${docId}&date=${date}`);
                            const data = await res.json();
                            const booked = new Set(data.appointments.map(a => a.appointment_time.substring(0,5)));
                            const locked = new Set(data.locks.map(l => l.appointment_time.substring(0,5)));

                            const times = timeRange('09:00','17:00',30);
                            const options = ['<option value="">Select time</option>'];
                            times.forEach(t => {
                                const disabled = booked.has(t) || locked.has(t);
                                if (!disabled) options.push(`<option value="${t}">${t}</option>`);
                            });
                            timeEl.innerHTML = options.join('');
                        }

                        doctorEl.addEventListener('change', refreshTimes);
                        dateEl.addEventListener('change', refreshTimes);

                        // Patient quick search select
                        (function initPatientQuickSelect(){
                            let debounceTimer = null;

                            function renderPatients(items){
                                if (!items || items.length === 0){
                                    patientResultsEl.innerHTML = '<div class="p-3 text-center text-gray-500">No patients found</div>';
                                } else {
                                    patientResultsEl.innerHTML = items.map(p => `
                                        <button type="button" data-id="${p.id}" data-name="${p.full_name}" class="w-full text-left p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="font-medium text-gray-900">${p.full_name}</div>
                                                    <div class="text-sm text-gray-500">ID: ${p.patient_id} • ${p.phone ?? ''}</div>
                                                    ${p.email ? `<div class=\"text-sm text-gray-500\">${p.email}</div>` : ''}
                                                </div>
                                                <div class="text-right">
                                                    ${p.age ? `<div class=\"text-sm text-gray-500\">${p.age} yrs</div>` : ''}
                                                    <div class="text-sm text-gray-500">${p.gender ?? ''}</div>
                                                </div>
                                            </div>
                                        </button>
                                    `).join('');
                                }
                                patientResultsEl.classList.remove('hidden');
                            }

                            function searchPatients(q){
                                if (!q || q.trim().length < 2){ patientResultsEl.classList.add('hidden'); return; }
                                fetch(`/search/patients?q=${encodeURIComponent(q)}`)
                                    .then(r => r.json())
                                    .then(renderPatients)
                                    .catch(() => { patientResultsEl.innerHTML = '<div class="p-3 text-center text-red-600">Search error</div>'; patientResultsEl.classList.remove('hidden'); });
                            }

                            patientSearchEl.addEventListener('input', function(){
                                clearTimeout(debounceTimer);
                                const q = this.value;
                                debounceTimer = setTimeout(() => searchPatients(q), 300);
                            });

                            patientResultsEl.addEventListener('click', function(e){
                                const btn = e.target.closest('button[data-id]');
                                if (!btn) return;
                                const id = btn.getAttribute('data-id');
                                const name = btn.getAttribute('data-name');
                                patientIdEl.value = id;
                                patientSearchEl.value = name;
                                patientResultsEl.classList.add('hidden');
                                patientClearBtn.classList.remove('hidden');
                                patientHint.classList.remove('hidden');
                                patientHint.textContent = `Selected: ${name} (ID ${id})`;
                            });

                            patientClearBtn.addEventListener('click', function(){
                                patientIdEl.value = '';
                                patientSearchEl.value = '';
                                patientClearBtn.classList.add('hidden');
                                patientHint.classList.add('hidden');
                                patientResultsEl.classList.add('hidden');
                                patientSearchEl.focus();
                            });

                            document.addEventListener('click', function(e){
                                if (!patientSearchEl.contains(e.target) && !patientResultsEl.contains(e.target)) {
                                    patientResultsEl.classList.add('hidden');
                                }
                            });
                            patientSearchEl.addEventListener('keydown', function(e){ if (e.key === 'Escape') { patientResultsEl.classList.add('hidden'); patientSearchEl.blur(); } });
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
