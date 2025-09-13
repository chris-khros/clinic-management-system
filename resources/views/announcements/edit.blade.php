<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Announcement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('announcements.update', $announcement->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Title</label>
                        <input type="text" name="title" value="{{ old('title', $announcement->title) }}" class="mt-1 block w-full border-gray-300 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Content</label>
                        <textarea name="content" rows="5" class="mt-1 block w-full border-gray-300 rounded" required>{{ old('content', $announcement->content) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Visibility</label>
                        <select name="visibility" class="mt-1 block w-full border-gray-300 rounded">
                            <option value="public" {{ $announcement->visibility === 'public' ? 'selected' : '' }}>Public</option>
                            <option value="staff" {{ $announcement->visibility === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="patients" {{ $announcement->visibility === 'patients' ? 'selected' : '' }}>Patients</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Expires At</label>
                        <input type="date" name="expires_at" value="{{ $announcement->expires_at ? $announcement->expires_at->format('Y-m-d') : '' }}" class="mt-1 block w-full border-gray-300 rounded">
                    </div>

                    <!--WhatsApp Checkbox-->
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="send_whatsapp" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="ml-2 text-gray-700">Resend this announcement via WhatsApp</span>
                        </label>
                    </div>

                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Update
                    </button>
                    <a href="{{ route('announcements.index') }}" class="ml-2 text-gray-600">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
