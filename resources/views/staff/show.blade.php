<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Details') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center space-x-6">
                    @if(!empty($staff->photo))
                        <img src="{{ asset('storage/'.$staff->photo) }}" alt="photo" class="w-28 h-28 rounded object-cover">
                    @else
                        <div class="w-28 h-28 rounded bg-gray-100 flex items-center justify-center text-gray-400">No Photo</div>
                    @endif

                    <div>
                        <h3 class="text-lg font-semibold">{{ $staff->full_name }}</h3>
                        <p class="text-sm text-gray-500">Employee ID: {{ $staff->employee_id ?? '-' }}</p>
                        <p class="text-sm text-gray-500">Role: {{ ucfirst($staff->role ?? ($staff->user->role ?? '-')) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600"><strong>Email:</strong> {{ $staff->email ?? ($staff->user->email ?? '-') }}</p>
                        <p class="text-sm text-gray-600"><strong>Phone:</strong> {{ $staff->phone ?? '-' }}</p>
                        <p class="text-sm text-gray-600"><strong>Hire Date:</strong> {{ optional($staff->hire_date)->format('Y-m-d') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600"><strong>Department:</strong> {{ $staff->department ?? '-' }}</p>
                        <p class="text-sm text-gray-600"><strong>Position:</strong> {{ $staff->position ?? '-' }}</p>
                        <p class="text-sm text-gray-600"><strong>Qualifications:</strong> {{ $staff->qualifications ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('staff.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Back</a>
                    <a href="{{ route('staff.edit', $staff->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Edit</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
