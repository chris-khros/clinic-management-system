<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Consultation') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Patient Info Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex items-center">
                    <img class="h-16 w-16 rounded-full mr-4" src="{{ $appointment->patient->photo ? asset('storage/' . $appointment->patient->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($appointment->patient->full_name) . '&color=7F9CF5&background=EBF4FF' }}" alt="Patient Photo">
                    <div>
                        <h3 class="text-xl font-bold">{{ $appointment->patient->full_name }}</h3>
                        <p class="text-sm text-gray-500">Appointment Date: {{ $appointment->appointment_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column: Patient History -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Patient History</h3>
                            <div class="space-y-4">
                                <!-- Timeline Item -->
                                <div class="flex">
                                    <div class="flex-shrink-0 w-12 text-center">
                                        <span class="block text-sm font-semibold">Oct 12</span>
                                        <span class="block text-xs text-gray-500">2023</span>
                                    </div>
                                    <div class="ml-4 border-l-2 pl-4">
                                        <h4 class="text-sm font-semibold">Routine Check-up</h4>
                                        <p class="text-sm text-gray-600">Dr. Smith - General Physician</p>
                                    </div>
                                </div>
                                <!-- Add more timeline items here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Consultation Form -->
                <div class="md:col-span-2">
                    <form action="{{ route('consultations.store') }}" method="POST" enctype="multipart/form-data" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                        <input type="hidden" name="doctor_id" value="{{ $appointment->doctor_id }}">

                        <div class="p-6 space-y-6">
                            @if ($errors->any())
                                <div class="mb-4 p-4 rounded bg-red-50 text-red-700">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <!-- Symptoms -->
                            <div>
                                <label for="symptoms" class="block text-sm font-medium text-gray-700">Symptoms</label>
                                <textarea name="symptoms" id="symptoms" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Describe the patient's symptoms..."></textarea>
                            </div>

                            <!-- Diagnosis -->
                            <div>
                                <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                                <textarea name="diagnosis" id="diagnosis" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Enter the diagnosis..."></textarea>
                            </div>

                            <!-- Treatment Plan -->
                            <div>
                                <label for="treatment_plan" class="block text-sm font-medium text-gray-700">Treatment Plan</label>
                                <textarea name="treatment_plan" id="treatment_plan" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Outline the treatment plan..."></textarea>
                            </div>

                            <!-- Consultation Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Consultation Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="" disabled selected>Select status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="follow_up">Follow-up Required</option>
                                </select>
                            </div>

                            <!-- E-Prescriptions and Reports -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Upload E-Prescriptions and Reports</label>
                                <div class="mt-2">
                                    <div id="documents_drop" class="rounded-lg border-2 border-dashed border-gray-300 hover:border-indigo-400 transition bg-gray-50">
                                        <label for="documents" class="cursor-pointer block p-6 text-center">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            <div class="mt-2 text-sm">
                                                <span class="font-medium text-gray-800">Choose or drop files</span>
                                                <span class="block text-gray-500">PDF, JPG, PNG up to 10MB each</span>
                                            </div>
                                        </label>
                                        <input id="documents" type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                        <ul id="documents_list" class="px-4 pb-3 text-xs text-gray-600 space-y-1"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end p-6 bg-gray-50">
                            <a href="{{ route('consultations.index') }}" class="bg-gray-200 text-gray-800 py-2 px-4 rounded-lg mr-4">Cancel</a>
                            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg">Save Consultation</button>
                        </div>
                    </form>
                    <script>
                        (function() {
                            function bindPreview(inputId, listId) {
                                var input = document.getElementById(inputId);
                                var list = document.getElementById(listId);
                                if (!input || !list) return;
                                input.addEventListener('change', function() {
                                    list.innerHTML = '';
                                    Array.from(input.files).forEach(function(file) {
                                        var li = document.createElement('li');
                                        li.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                                        list.appendChild(li);
                                    });
                                });
                            }

                            bindPreview('documents', 'documents_list');

                            function bindDrop(dropId, inputId) {
                                var drop = document.getElementById(dropId);
                                var input = document.getElementById(inputId);
                                if (!drop || !input) return;
                                ;['dragenter','dragover'].forEach(function(evt){
                                    drop.addEventListener(evt, function(e){ e.preventDefault(); drop.classList.add('border-indigo-400','bg-indigo-50'); });
                                });
                                ;['dragleave','drop'].forEach(function(evt){
                                    drop.addEventListener(evt, function(e){ e.preventDefault(); drop.classList.remove('border-indigo-400','bg-indigo-50'); });
                                });
                                drop.addEventListener('drop', function(e){
                                    e.preventDefault();
                                    if (!e.dataTransfer || !e.dataTransfer.files) return;
                                    // Merge newly dropped files with existing FileList using DataTransfer
                                    var dt = new DataTransfer();
                                    Array.from(input.files).forEach(function(f){ dt.items.add(f); });
                                    Array.from(e.dataTransfer.files).forEach(function(f){ dt.items.add(f); });
                                    input.files = dt.files;
                                    input.dispatchEvent(new Event('change'));
                                });
                            }

                            bindDrop('documents_drop', 'documents');
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
