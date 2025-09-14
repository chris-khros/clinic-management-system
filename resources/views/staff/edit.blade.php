<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Staff Information
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Full Name --}}
                        <div class="mb-4">
                            <label class="block font-medium">Full Name</label>
                            <input type="text" name="full_name" class="w-full border rounded p-2"
                                   value="{{ old('full_name', $staff->full_name) }}" required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block font-medium">Email</label>
                            <input type="email" name="email" class="w-full border rounded p-2"
                                   value="{{ old('email', $staff->email) }}" required>
                        </div>

                        {{-- Phone --}}
                        <div class="mb-4">
                            <label class="block font-medium">Phone</label>
                            <input type="text" name="phone" class="w-full border rounded p-2"
                                   value="{{ old('phone', $staff->phone) }}">
                        </div>

                        {{-- Department --}}
                        <div class="mb-4">
                            <label class="block font-medium">Department</label>
                            <input type="text" name="department" class="w-full border rounded p-2"
                                   value="{{ old('department', $staff->department) }}">
                        </div>

                        {{-- Position --}}
                        <div class="mb-4">
                            <label class="block font-medium">Position</label>
                            <input type="text" name="position" class="w-full border rounded p-2"
                                   value="{{ old('position', $staff->position) }}">
                        </div>

                        {{-- Role --}}
                        <div class="mb-4">
                            <label class="block font-medium">Role</label>
                            <select name="role" class="w-full border rounded p-2">
                                <option value="admin" {{ $staff->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="doctor" {{ $staff->role == 'doctor' ? 'selected' : '' }}>Doctor</option>
                                <option value="nurse" {{ $staff->role == 'nurse' ? 'selected' : '' }}>Nurse</option>
                                <option value="receptionist" {{ $staff->role == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                                <option value="pharmacist" {{ $staff->role == 'pharmacist' ? 'selected' : '' }}>Pharmacist</option>
                            </select>
                        </div>

                        {{-- Hire Date --}}
                        <div class="mb-4">
                            <label class="block font-medium">Hire Date</label>
                            <input type="date" name="hire_date" class="w-full border rounded p-2"
                                   value="{{ old('hire_date', $staff->hire_date ? $staff->hire_date->format('Y-m-d') : '') }}">
                        </div>

                        {{-- Photo (optional) --}}
                        <div class="mb-4">
                            <label class="block font-medium">Photo</label>
                            <input type="file" name="photo" class="w-full border rounded p-2">
                            @if($staff->photo)
                                <img src="{{ asset('storage/' . $staff->photo) }}" alt="Staff Photo" class="h-20 mt-2">
                            @endif
                        </div>

                        {{-- Is Active --}}
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $staff->is_active ? 'checked' : '' }}>
                                <span class="ml-2">Active</span>
                            </label>
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Update Staff
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
