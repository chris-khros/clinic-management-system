<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Staff') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('staff.update', $staff->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Photo Preview -->
                    <div class="mb-6 flex items-center space-x-6">
                        @if(!empty($staff->photo))
                            <img src="{{ asset('storage/'.$staff->photo) }}" 
                                 alt="Staff Photo" 
                                 class="w-28 h-28 rounded-full object-cover border shadow">
                        @else
                            <div class="w-28 h-28 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                No Photo
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload New Photo</label>
                            <input type="file" name="photo" class="mt-2 block w-full text-sm text-gray-700 border rounded p-1">
                            <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG (Max 2MB)</p>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $staff->full_name) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $staff->email) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ $staff->role == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <input type="text" name="department" value="{{ old('department', $staff->department) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- Position -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" name="position" value="{{ old('position', $staff->position) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- Qualifications -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Qualifications</label>
                        <textarea name="qualifications" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('qualifications', $staff->qualifications) }}</textarea>
                    </div>

                    <!-- Hire Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Hire Date</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date', $staff->hire_date ? $staff->hire_date->format('Y-m-d') : '') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
