<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointments Calendar') }}
        </h2>
    </x-slot>

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap gap-4 items-end mb-4">
                        @if(auth()->user()->role !== 'doctor')
                        <div>
                            <label for="doctor_id" class="block text-sm font-medium mb-1">Doctor</label>
                            <select id="doctor_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(\App\Models\Doctor::where('is_active', true)->get() as $doc)
                                    <option value="{{ $doc->id }}">
                                        {{ $doc->user->name ?? ('Doctor #'.$doc->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="hidden">
                            <select id="doctor_id">
                                <option value="{{ auth()->user()->doctor->id }}" selected>
                                    {{ auth()->user()->doctor->user->name ?? ('Doctor #'.auth()->user()->doctor->id) }}
                                </option>
                            </select>
                        </div>
                        @endif
                        <div class="ml-auto flex flex-wrap items-center gap-4">
                            <!-- Appointment Status Legend -->
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                                <span class="text-sm">Scheduled</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-green-500"></span>
                                <span class="text-sm">Confirmed</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-amber-500"></span>
                                <span class="text-sm">In Progress</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-gray-500"></span>
                                <span class="text-sm">Completed</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-red-500"></span>
                                <span class="text-sm">No Show</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                                <span class="text-sm">Cancelled</span>
                            </div>

                            <!-- Locked slots -->
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-yellow-400"></span>
                                <span class="text-sm">Locked</span>
                            </div>
                        </div>
                    </div>

                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Book Appointment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        You are booking a slot for <strong id="modal-time"></strong> on <strong id="modal-date"></strong>.
                    </p>
                    <div class="mt-4">
                         <label for="patient-select" class="block text-sm font-medium text-gray-700 text-left">Select Patient</label>
                         <select id="patient-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <!-- Patient options will be loaded here -->
                         </select>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirm-booking" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Confirm Booking
                    </button>
                    <button id="cancel-booking" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const doctorEl = document.getElementById('doctor_id');
            const bookingModal = document.getElementById('booking-modal');
            const confirmBookingBtn = document.getElementById('confirm-booking');
            const cancelBookingBtn = document.getElementById('cancel-booking');
            const patientSelect = document.getElementById('patient-select');
            let calendar;
            let selectedSlot = {};

            function initializeCalendar() {
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    slotMinTime: '09:00:00',
                    slotMaxTime: '18:00:00',
                    slotDuration: '00:30:00',
                    selectable: true,
                    events: fetchEvents,
                    dateClick: function(info) {
                        if (info.date < new Date()) {
                            alert("Cannot book appointments in the past.");
                            return;
                        }

                        const isBooked = calendar.getEvents().some(event => {
                            return info.date >= event.start && info.date < event.end;
                        });

                        if (isBooked) {
                            alert("This time slot is not available.");
                            return;
                        }

                        selectedSlot.start = info.date;
                        document.getElementById('modal-date').innerText = info.date.toLocaleDateString();
                        document.getElementById('modal-time').innerText = info.date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        bookingModal.classList.remove('hidden');
                        loadPatients();
                    },
                    eventClick: function(info) {
                        const status = info.event.extendedProps?.status || 'unknown';
                        const patient = info.event.extendedProps?.patient;
                        const patientInfo = patient ? `${patient.full_name}\nPhone: ${patient.phone}\nEmail: ${patient.email}` : 'No patient info';

                        const message = `Appointment Details:\n\nPatient: ${patientInfo}\nStatus: ${status.replace('_', ' ').toUpperCase()}\nTime: ${info.event.start.toLocaleString()}\n\nDo you want to manage this appointment?`;

                        if(confirm(message)) {
                            // Here you could add logic to edit/manage the appointment
                            alert('Appointment management feature would open here.');
                        }
                    }
                });
                calendar.render();
            }

            function fetchEvents(fetchInfo, successCallback, failureCallback) {
                const doctorId = doctorEl.value;
                const startDate = new Date(fetchInfo.start).toISOString().slice(0, 10);
                const endDate = new Date(fetchInfo.end).toISOString().slice(0, 10);

                fetch(`{{ route('appointments.doctor-schedule') }}?doctor_id=${doctorId}&date=${startDate}&end_date=${endDate}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const events = [];

                        if (data.appointments && Array.isArray(data.appointments)) {
                            data.appointments.forEach(app => {
                                const startDateTime = `${app.appointment_date}T${app.appointment_time}`;

                                // Color coding based on appointment status
                                const statusColors = {
                                    'scheduled': { bg: '#3B82F6', border: '#3B82F6' },    // Blue
                                    'confirmed': { bg: '#10B981', border: '#10B981' },    // Green
                                    'in_progress': { bg: '#F59E0B', border: '#F59E0B' },  // Amber
                                    'completed': { bg: '#6B7280', border: '#6B7280' },    // Gray
                                    'no_show': { bg: '#EF4444', border: '#EF4444' },      // Red
                                    'cancelled': { bg: '#9CA3AF', border: '#9CA3AF' }     // Light Gray
                                };

                                const colors = statusColors[app.status] || statusColors['scheduled'];

                                events.push({
                                    title: app.patient ? `${app.patient.full_name} (${app.status.replace('_', ' ')})` : `Booked (${app.status.replace('_', ' ')})`,
                                    start: startDateTime,
                                    backgroundColor: colors.bg,
                                    borderColor: colors.border,
                                    extendedProps: {
                                        status: app.status,
                                        patient: app.patient
                                    }
                                });
                            });
                        }

                        if (data.locks && Array.isArray(data.locks)) {
                            data.locks.forEach(lock => {
                                const startDateTime = `${lock.appointment_date}T${lock.appointment_time}`;

                                events.push({
                                    title: 'Locked',
                                    start: startDateTime,
                                    backgroundColor: '#FBBF24', // yellow-400
                                    borderColor: '#FBBF24'
                                });
                            });
                        }

                        successCallback(events);
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                    });
            }

            async function loadPatients() {
                try {
                    const response = await fetch('{{ route("patients.index") }}?json=true');
                    const patients = await response.json();
                    patientSelect.innerHTML = '<option>Select a patient</option>';
                    patients.forEach(patient => {
                        const option = document.createElement('option');
                        option.value = patient.id;
                        option.textContent = `${patient.full_name} (${patient.email})`;
                        patientSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Failed to load patients:', error);
                    patientSelect.innerHTML = '<option>Could not load patients</option>';
                }
            }

            function hideModal() {
                bookingModal.classList.add('hidden');
            }

            doctorEl.addEventListener('change', () => calendar.refetchEvents());

            cancelBookingBtn.addEventListener('click', hideModal);

            confirmBookingBtn.addEventListener('click', async () => {
                const patientId = patientSelect.value;
                if (!patientId || isNaN(patientId)) {
                    alert('Please select a patient.');
                    return;
                }

                const appointmentData = {
                    doctor_id: doctorEl.value,
                    patient_id: patientId,
                    appointment_date: selectedSlot.start.toISOString().slice(0, 10),
                    appointment_time: selectedSlot.start.toTimeString().slice(0, 8),
                    status: 'scheduled',
                    _token: '{{ csrf_token() }}'
                };

                try {
                    const response = await fetch('{{ route("appointments.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(appointmentData)
                    });

                    if (response.ok) {
                        alert('Appointment booked successfully!');
                        hideModal();
                        calendar.refetchEvents();
                    } else {
                        const errorData = await response.json();
                        alert('Failed to book appointment: ' + (errorData.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error booking appointment:', error);
                    alert('An error occurred while booking the appointment.');
                }
            });

            initializeCalendar();
        });
    </script>
</x-app-layout>



