<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Consultation Management') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold">All Consultations</h3>
                            @if(request()->hasAny(['status', 'date_from', 'date_to']))
                                <p class="text-sm text-gray-600 mt-1">
                                    Showing {{ $consultations->count() }} of {{ $consultations->total() }} consultations
                                    @if(request('status'))
                                        with status "{{ ucfirst(str_replace('_', ' ', request('status'))) }}"
                                    @endif
                                    @if(request('date_from') || request('date_to'))
                                        @if(request('date_from') && request('date_to'))
                                            from {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} to {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                        @elseif(request('date_from'))
                                            from {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                        @elseif(request('date_to'))
                                            until {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                        @endif
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('consultations.index') }}" class="mb-6">
                        <div class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="border rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="follow_up" {{ request('status') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                </select>
                            </div>
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="border rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="border rounded-lg py-2 px-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('consultations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Consultations Table -->
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($consultations as $consultation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $consultation->appointment->patient->full_name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $consultation->appointment->patient->patient_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $consultation->doctor->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $consultation->appointment->appointment_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($consultation->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($consultation->status === 'completed') bg-green-100 text-green-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($consultation->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('consultations.show', $consultation) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <a href="{{ route('consultations.edit', $consultation) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">
                                                    @if(request()->hasAny(['status', 'date_from', 'date_to']))
                                                        No consultations match your filters
                                                    @else
                                                        No consultations found
                                                    @endif
                                                </h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    @if(request()->hasAny(['status', 'date_from', 'date_to']))
                                                        Try adjusting your search criteria or clear the filters.
                                                    @else
                                                        Get started by creating a new consultation.
                                                    @endif
                                                </p>
                                                @if(request()->hasAny(['status', 'date_from', 'date_to']))
                                                    <div class="mt-4">
                                                        <a href="{{ route('consultations.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Clear Filters
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $consultations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
