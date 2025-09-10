<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Management') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('staff.create') }}" 
                       class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Add Staff
                    </a>
                </div>

                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 px-4 py-2">Photo</th>
                            <th class="border border-gray-200 px-4 py-2">Full Name</th>
                            <th class="border border-gray-200 px-4 py-2">Role</th>
                            <th class="border border-gray-200 px-4 py-2">Department</th>
                            <th class="border border-gray-200 px-4 py-2">Position</th>
                            <th class="border border-gray-200 px-4 py-2">Phone</th>
                            <th class="border border-gray-200 px-4 py-2">Email</th>
                            <th class="border border-gray-200 px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $member)
                            <tr>
                                <!-- Photo -->
                                <td class="border border-gray-200 px-4 py-2 text-center">
                                    @if(!empty($member->photo))
                                        <img src="{{ asset('storage/' . $member->photo) }}" 
                                             alt="Staff Photo" 
                                             class="w-12 h-12 rounded-full object-cover mx-auto">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mx-auto">
                                            <span class="text-xs">No Photo</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Name -->
                                <td class="border border-gray-200 px-4 py-2">{{ $member->full_name }}</td>

                                <!-- Role -->
                                <td class="border border-gray-200 px-4 py-2">{{ ucfirst($member->role ?? ($member->user->role ?? '-')) }}</td>

                                <!-- Department -->
                                <td class="border border-gray-200 px-4 py-2">{{ $member->department }}</td>

                                <!-- Position -->
                                <td class="border border-gray-200 px-4 py-2">{{ $member->position }}</td>

                                <!-- Phone -->
                                <td class="border border-gray-200 px-4 py-2">{{ $member->phone }}</td>

                                <!-- Email -->
                                <td class="border border-gray-200 px-4 py-2">{{ $member->email }}</td>

                                <!-- Actions -->
                                <td class="border border-gray-200 px-4 py-2 flex space-x-2 justify-center">
                                    <a href="{{ route('staff.show', $member->id) }}" 
                                       class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</a>
                                    <a href="{{ route('staff.edit', $member->id) }}" 
                                       class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                                    <form action="{{ route('staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
