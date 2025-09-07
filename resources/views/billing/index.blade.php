<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Billing & Invoicing') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h4 class="text-gray-500 text-sm font-medium">Total Revenue</h4>
                    <p class="text-3xl font-bold">${{ number_format($total_revenue, 2) }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h4 class="text-gray-500 text-sm font-medium">Outstanding</h4>
                    <p class="text-3xl font-bold">${{ number_format($outstanding_revenue, 2) }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h4 class="text-gray-500 text-sm font-medium">Paid Invoices</h4>
                    <p class="text-3xl font-bold">{{ $paid_bills_count }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold">All Invoices</h3>
                        <a href="{{ route('billing.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Create New Bill
                        </a>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('billing.index') }}" class="mb-6">
                        <div class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Status</label>
                                <select name="status" class="border rounded-lg py-2 px-4">
                                    <option value="">All Statuses</option>
                                    <option value="paid" {{ request('status')==='paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="unpaid" {{ request('status')==='unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">From</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded-lg py-2 px-4">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">To</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded-lg py-2 px-4">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Filter</button>
                                <a href="{{ route('billing.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Invoices Table -->
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bills as $bill)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $bill->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bill->patient->full_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ($bill->bill_date ?? $bill->created_at)->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($bill->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($bill->payment_status === 'paid') bg-green-100 text-green-800
                                                @elseif($bill->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($bill->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('billing.invoice', $bill) }}" class="text-blue-600 hover:text-blue-900">Invoice</a>
                                            <a href="{{ route('billing.edit', $bill) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No bills found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $bills->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
