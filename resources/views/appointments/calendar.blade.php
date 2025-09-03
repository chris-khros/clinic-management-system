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
                        <div>
                            <label for="doctor_id" class="block text-sm font-medium mb-1">Doctor</label>
                            <select id="doctor_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(\App\Models\Doctor::where('is_active', true)->get() as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->user->name ?? ('Doctor #'.$doc->id) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ml-auto flex items-center gap-4">
                             <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                                <span class="text-sm">Booked</span>
                            </div>
                             <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-yellow-400"></span>
                                <span class="text-sm">Locked</span>
                            </div>
                             <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-green-500"></span>
                                <span class="text-sm">Available</span>
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
                        if(confirm("Do you want to cancel this appointment?")) {
                            // Here you would add logic to cancel the appointment
                            alert('Appointment for ' + info.event.title + ' cancelled.');
                            info.event.remove();
                        }
                    }
                });
                calendar.render();
            }

            function fetchEvents(fetchInfo, successCallback, failureCallback) {
                const doctorId = doctorEl.value;
                const date = new Date(fetchInfo.start).toISOString().slice(0, 10);

                fetch(`{{ route('appointments.doctor-schedule') }}?doctor_id=${doctorId}&date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        const events = [];
                        data.appointments.forEach(app => {
                            events.push({
                                title: app.patient ? `${app.patient.first_name} ${app.patient.last_name}` : 'Booked',
                                start: `${app.appointment_date}T${app.appointment_time}`,
                                backgroundColor: '#3B82F6', // blue-500
                                borderColor: '#3B82F6'
                            });
                        });
                        data.locks.forEach(lock => {
                            events.push({
                                title: 'Locked',
                                start: `${lock.appointment_date}T${lock.appointment_time}`,
                                backgroundColor: '#FBBF24', // yellow-400
                                borderColor: '#FBBF24'
                            });
                        });
                        successCallback(events);
                    })
                    .catch(error => failureCallback(error));
            }

            async function loadPatients() {
                try {
                    const response = await fetch('{{ route("patients.index") }}?json=true');
                    const patients = await response.json();
                    patientSelect.innerHTML = '<option>Select a patient</option>';
                    patients.forEach(patient => {
                        const option = document.createElement('option');
                        option.value = patient.id;
                        option.textContent = `${patient.first_name} ${patient.last_name} (${patient.email})`;
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



