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
                            <select name="patient_id" id="patient_id" class="border rounded p-2 w-full" required>
                                <option value="">Select a patient</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}">{{ $p->full_name ?? ('#'.$p->id) }}</option>
                                @endforeach
                            </select>
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
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
