<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Patient') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Personal Details -->
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Personal Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="full_name" id="full_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="" selected disabled>Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="text" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div class="col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Emergency Contact</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Patient Photo -->
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold mb-4">Patient Photo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                                <div>
                                    <video id="webcam" autoplay class="w-full rounded-lg border"></video>
                                    <button type="button" id="snap" class="mt-2 w-full bg-blue-500 text-white py-2 px-4 rounded-lg">Take Photo</button>
                                </div>
                                <div>
                                    <canvas id="canvas" class="hidden"></canvas>
                                    <img id="photo-preview" src="" alt="Patient Photo Preview" class="w-full rounded-lg border hidden">
                                    <input type="hidden" name="photo_data" id="photo-data">
                                </div>
                            </div>
                        </div>

                        <!-- Document Upload -->
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Upload Documents</h3>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="documents" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="documents" name="documents[]" type="file" class="sr-only" multiple>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 10MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end p-6">
                            <a href="{{ route('patients.index') }}" class="bg-gray-200 text-gray-800 py-2 px-4 rounded-lg mr-4">Cancel</a>
                            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg">Save Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const webcamElement = document.getElementById('webcam');
            const canvasElement = document.getElementById('canvas');
            const snapButton = document.getElementById('snap');
            const photoPreview = document.getElementById('photo-preview');
            const photoDataInput = document.getElementById('photo-data');

            if (navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(stream => {
                        webcamElement.srcObject = stream;
                    })
                    .catch(error => {
                        console.error("Error accessing webcam: ", error);
                        alert('Could not access the webcam. Please ensure you have a webcam enabled and have granted permission.');
                    });
            }

            snapButton.addEventListener('click', () => {
                const context = canvasElement.getContext('2d');
                canvasElement.width = webcamElement.videoWidth;
                canvasElement.height = webcamElement.videoHeight;
                context.drawImage(webcamElement, 0, 0, webcamElement.videoWidth, webcamElement.videoHeight);

                const dataUrl = canvasElement.toDataURL('image/png');
                photoPreview.src = dataUrl;
                photoPreview.classList.remove('hidden');
                photoDataInput.value = dataUrl;
                webcamElement.classList.add('hidden');
            });
        });
    </script>
</x-app-layout>
