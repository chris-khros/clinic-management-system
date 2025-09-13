<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $announcement->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <p class="mb-4 text-gray-700">{{ $announcement->content }}</p>

                <p class="text-sm text-gray-500">
                    <strong>Visibility:</strong> {{ ucfirst($announcement->visibility) }} |
                    <strong>Status:</strong> {{ $announcement->is_active ? 'Active' : 'Inactive' }} |
                    <strong>Expires:</strong> {{ $announcement->expires_at ? $announcement->expires_at->format('Y-m-d') : 'No Expiry' }}
                </p>

                <div class="mt-6">
                    <a href="{{ route('announcements.index') }}" class="text-blue-600 hover:underline">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
