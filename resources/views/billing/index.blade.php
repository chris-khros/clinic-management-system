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
                    <!-- Patient Quick Search & Filter -->
                    <form id="billing-filter-form" method="GET" action="{{ route('billing.index') }}" class="mb-6">
                        <input type="hidden" id="billing_patient_id" name="patient_id" value="{{ request('patient_id') }}">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Patient Quick Search</h3>
                        <div class="relative">
                            <input type="text"
                                   id="billing-patient-search"
                                   placeholder="Search patient by name, ID, phone, or email..."
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div id="billing-search-results" class="mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-96 overflow-y-auto"></div>
                    </form>
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
    <script>
        (function initBillingSearch(){
            const input = document.getElementById('billing-patient-search');
            const results = document.getElementById('billing-search-results');
            if (!input || !results) return;

            let t = null;
            function render(items){
                if (!items || items.length === 0){
                    results.innerHTML = '<div class="p-4 text-gray-500 text-center">No patients found</div>';
                } else {
                    results.innerHTML = items.map(p => `
                        <button type="button" data-id="${p.id}" class="w-full text-left p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">${p.full_name}</div>
                                    <div class="text-sm text-gray-500">ID: ${p.patient_id} • ${p.phone ?? ''}</div>
                                    ${p.email ? `<div class=\"text-sm text-gray-500\">${p.email}</div>` : ''}
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">${p.gender ?? ''}</div>
                                    ${p.age ? `<div class=\"text-sm text-gray-500\">${p.age} years</div>` : ''}
                                </div>
                            </div>
                        </button>
                    `).join('');
                }
                results.classList.remove('hidden');
            }

            function search(q){
                if (!q || q.trim().length < 2){ results.classList.add('hidden'); return; }
                fetch(`/search/patients?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(render)
                    .catch(() => { results.innerHTML = '<div class="p-4 text-red-600 text-center">Search error</div>'; results.classList.remove('hidden'); });
            }

            input.addEventListener('input', function(){
                clearTimeout(t);
                const q = this.value;
                t = setTimeout(() => search(q), 300);
            });

            results.addEventListener('click', function(e){
                const btn = e.target.closest('button[data-id]');
                if (!btn) return;
                document.getElementById('billing_patient_id').value = btn.getAttribute('data-id');
                document.getElementById('billing-filter-form').submit();
            });

            document.addEventListener('click', function(e){
                if (!input.contains(e.target) && !results.contains(e.target)) results.classList.add('hidden');
            });
            input.addEventListener('keydown', function(e){ if (e.key === 'Escape') { results.classList.add('hidden'); input.blur(); } });
        })();
    </script>
</x-app-layout>
