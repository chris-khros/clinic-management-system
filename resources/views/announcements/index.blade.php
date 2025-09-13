<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Announcements') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Announcement Management</h3>
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('announcements.create') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Announcement
                            </a>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Title</th>
                                <th class="px-4 py-2 border">Visibility</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Expires</th>
                                <th class="px-4 py-2 border">Created By</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $announcement)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $announcement->title }}</td>
                                    <td class="px-4 py-2 border capitalize">{{ $announcement->visibility }}</td>
                                    <td class="px-4 py-2 border">
                                        {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ $announcement->expires_at ? $announcement->expires_at->format('Y-m-d') : 'No Expiry' }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ $announcement->creator->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('announcements.show', $announcement) }}" class="text-blue-600 hover:underline mr-2">View</a>

                                        @if(Auth::user()->role === 'admin')
                                            <a href="{{ route('announcements.edit', $announcement) }}" class="text-green-600 hover:underline mr-2">Edit</a>

                                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>

                                            {{-- Toggle Status via update (PATCH) --}}
                                            <form action="{{ route('announcements.update', $announcement->id) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="toggle_status" value="1">
                                                <button type="submit" class="text-yellow-600 hover:underline">
                                                    {{ $announcement->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">No announcements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $announcements->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
