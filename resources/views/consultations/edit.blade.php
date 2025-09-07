<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Consultation') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('consultations.show', $consultation) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">Back to Consultation</a>
                <a href="{{ route('consultations.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">All Consultations</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Patient Info Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img class="h-16 w-16 rounded-full mr-4" src="{{ $consultation->patient->photo ? asset('storage/' . $consultation->patient->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($consultation->patient->first_name . ' ' . $consultation->patient->last_name) . '&color=7F9CF5&background=EBF4FF' }}" alt="Patient Photo">
                            <div>
                                <h3 class="text-xl font-bold">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</h3>
                                <p class="text-sm text-gray-500">Patient ID: {{ $consultation->patient->id }}</p>
                                <p class="text-sm text-gray-500">Consultation Date: {{ $consultation->consultation_date->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Doctor: {{ $consultation->doctor->user->name ?? 'Dr. Unknown' }}</p>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($consultation->status === 'completed') bg-green-100 text-green-800
                                @elseif($consultation->status === 'follow_up') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($consultation->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('consultations.update', $consultation) }}" method="POST" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-8">
                    <!-- Symptoms Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Symptoms & Chief Complaint</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="chief_complaint" class="block text-sm font-medium text-gray-700">Chief Complaint</label>
                                <input type="text" name="chief_complaint" id="chief_complaint" value="{{ old('chief_complaint', $consultation->chief_complaint) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Primary reason for visit">
                            </div>
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700">Duration</label>
                                <select name="duration" id="duration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select duration</option>
                                    <option value="acute" {{ old('duration', $consultation->duration) === 'acute' ? 'selected' : '' }}>Acute (< 1 week)</option>
                                    <option value="subacute" {{ old('duration', $consultation->duration) === 'subacute' ? 'selected' : '' }}>Subacute (1-4 weeks)</option>
                                    <option value="chronic" {{ old('duration', $consultation->duration) === 'chronic' ? 'selected' : '' }}>Chronic (> 4 weeks)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="symptoms" class="block text-sm font-medium text-gray-700">Detailed Symptoms</label>
                            <textarea name="symptoms" id="symptoms" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe the patient's symptoms in detail...">{{ old('symptoms', $consultation->symptoms) }}</textarea>
                        </div>
                    </div>

                    <!-- Physical Examination -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Physical Examination</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="vital_signs" class="block text-sm font-medium text-gray-700">Vital Signs</label>
                                <textarea name="vital_signs" id="vital_signs" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="BP, HR, Temp, RR, etc.">{{ old('vital_signs', $consultation->vital_signs) }}</textarea>
                            </div>
                            <div>
                                <label for="physical_findings" class="block text-sm font-medium text-gray-700">Physical Findings</label>
                                <textarea name="physical_findings" id="physical_findings" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="General appearance, system examination...">{{ old('physical_findings', $consultation->physical_findings) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnosis Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Diagnosis</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="primary_diagnosis" class="block text-sm font-medium text-gray-700">Primary Diagnosis</label>
                                <input type="text" name="primary_diagnosis" id="primary_diagnosis" value="{{ old('primary_diagnosis', $consultation->primary_diagnosis) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Main diagnosis">
                            </div>
                            <div>
                                <label for="secondary_diagnosis" class="block text-sm font-medium text-gray-700">Secondary Diagnosis</label>
                                <input type="text" name="secondary_diagnosis" id="secondary_diagnosis" value="{{ old('secondary_diagnosis', $consultation->secondary_diagnosis) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Additional diagnoses">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="diagnosis" class="block text-sm font-medium text-gray-700">Detailed Diagnosis</label>
                            <textarea name="diagnosis" id="diagnosis" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Detailed diagnosis with ICD-10 codes if applicable...">{{ old('diagnosis', $consultation->diagnosis) }}</textarea>
                        </div>
                    </div>

                    <!-- Treatment Plan -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Treatment Plan</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="medications" class="block text-sm font-medium text-gray-700">Medications Prescribed</label>
                                <textarea name="medications" id="medications" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="List medications with dosage and frequency...">{{ old('medications', $consultation->medications) }}</textarea>
                            </div>
                            <div>
                                <label for="treatment_plan" class="block text-sm font-medium text-gray-700">Treatment Plan</label>
                                <textarea name="treatment_plan" id="treatment_plan" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Detailed treatment plan including lifestyle modifications...">{{ old('treatment_plan', $consultation->treatment_plan) }}</textarea>
                            </div>
                            <div>
                                <label for="prescription" class="block text-sm font-medium text-gray-700">Prescription Notes</label>
                                <textarea name="prescription" id="prescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Additional prescription notes...">{{ old('prescription', $consultation->prescription) }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="follow_up_date" class="block text-sm font-medium text-gray-700">Follow-up Date</label>
                                    <input type="date" name="follow_up_date" id="follow_up_date" value="{{ old('follow_up_date', $consultation->follow_up_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Consultation Status</label>
                                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="completed" {{ old('status', $consultation->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="follow_up" {{ old('status', $consultation->status) === 'follow_up' ? 'selected' : '' }}>Follow-up Required</option>
                                        <option value="pending" {{ old('status', $consultation->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Any additional notes or observations...">{{ old('notes', $consultation->notes) }}</textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between p-6 bg-gray-50">
                    <div class="flex space-x-2">
                        <button type="button" onclick="printConsultation()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">Print</button>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('consultations.show', $consultation) }}" class="bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded">Cancel</a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">Update Consultation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function printConsultation() {
            window.print();
        }
    </script>
    @endpush
</x-app-layout>
