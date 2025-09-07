<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $bill->bill_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12px;
                margin: 0;
                padding: 20px;
            }

            .bg-white {
                background: white !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }

            .border {
                border: 1px solid #e5e7eb !important;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto bg-white shadow-sm rounded-lg">
            <!-- Header Actions (Hidden when printing) -->
            <div class="no-print p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('billing.index') }}" class="text-gray-600 hover:text-gray-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Invoice #{{ $bill->bill_number }}</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Invoice
                        </button>
                        <a href="{{ route('billing.show', $bill) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Invoice Content -->
            <div class="p-8">
                <!-- Invoice Header -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">INVOICE</h1>
                        <div class="text-gray-600">
                            <p class="font-semibold">Clinic Management System</p>
                            <p>123 Medical Center Drive</p>
                            <p>Healthcare City, HC 12345</p>
                            <p>Phone: (555) 123-4567</p>
                            <p>Email: billing@clinic.com</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Invoice Number</p>
                            <p class="text-xl font-bold">{{ $bill->bill_number }}</p>
                            <p class="text-sm text-gray-600 mt-2">Invoice Date</p>
                            <p class="font-semibold">{{ ($bill->bill_date ?? $bill->created_at)->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-600 mt-2">Due Date</p>
                            <p class="font-semibold">{{ $bill->due_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bill To Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bill To:</h3>
                        <div class="text-gray-700">
                            <p class="font-semibold">{{ $bill->patient->full_name }}</p>
                            <p>{{ $bill->patient->email }}</p>
                            <p>{{ $bill->patient->phone }}</p>
                            <p>DOB: {{ $bill->patient->date_of_birth->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information:</h3>
                        <div class="text-gray-700">
                            <p><span class="font-semibold">Status:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($bill->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($bill->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($bill->payment_status) }}
                                </span>
                            </p>
                            @if($bill->payment_method)
                            <p><span class="font-semibold">Method:</span> {{ ucfirst($bill->payment_method) }}</p>
                            @endif
                            @if($bill->paid_at)
                            <p><span class="font-semibold">Paid Date:</span> {{ $bill->paid_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <!-- Update Status Section (separate block, not printed) -->
                        <div class="no-print mt-4 p-4 bg-gray-50 rounded border">
                            <h4 class="font-semibold text-gray-900 mb-2">Update Payment Status</h4>
                            <form action="{{ route('billing.update-status', $bill) }}" method="POST" class="flex flex-wrap items-center gap-3">
                                @csrf
                                @method('PATCH')
                                <select name="payment_status" class="border rounded px-2 py-1 text-sm">
                                    <option value="pending" {{ $bill->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $bill->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="unpaid" {{ $bill->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                                <select name="payment_method" class="border rounded px-2 py-1 text-sm" title="Method (only used when Paid)">
                                    <option value="" {{ !$bill->payment_method ? 'selected' : '' }}>Method...</option>
                                    <option value="cash" {{ ($bill->payment_method ?? '') === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ ($bill->payment_method ?? '') === 'card' ? 'selected' : '' }}>Card</option>
                                    <option value="insurance" {{ ($bill->payment_method ?? '') === 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="online" {{ ($bill->payment_method ?? '') === 'online' ? 'selected' : '' }}>Online</option>
                                </select>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Services Table -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Services:</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Description</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Unit Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bill->billItems as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-b border-gray-200">
                                        {{ $item->service->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 border-b border-gray-200">
                                        {{ $item->description ?? $item->service->description ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center border-b border-gray-200">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right border-b border-gray-200">
                                        ${{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right border-b border-gray-200">
                                        ${{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals -->
                <div class="flex justify-end mb-8">
                    <div class="w-80">
                        <div class="space-y-2">
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">${{ number_format($bill->subtotal, 2) }}</span>
                            </div>
                            @if($bill->discount_amount > 0)
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Discount:</span>
                                <span class="font-medium text-green-600">-${{ number_format($bill->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Tax (10%):</span>
                                <span class="font-medium">${{ number_format($bill->tax_amount, 2) }}</span>
                            </div>
                            <div class="border-t-2 border-gray-300 pt-2">
                                <div class="flex justify-between py-2">
                                    <span class="text-xl font-bold text-gray-900">Total Amount:</span>
                                    <span class="text-xl font-bold text-gray-900">${{ number_format($bill->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($bill->notes)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes:</h3>
                    <div class="text-gray-700 bg-gray-50 p-4 rounded-lg border">
                        {{ $bill->notes }}
                    </div>
                </div>
                @endif

                <!-- Footer -->
                <div class="border-t border-gray-200 pt-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Payment Instructions:</h4>
                            <p class="text-sm text-gray-600">
                                Please make payment by the due date. For questions about this invoice,
                                please contact our billing department at (555) 123-4567.
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Thank you for choosing our services!</p>
                            <p class="text-sm text-gray-500 mt-2">{{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
