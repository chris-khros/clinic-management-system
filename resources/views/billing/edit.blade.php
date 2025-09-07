<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Invoice') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('billing.update', $bill) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 md:p-8 text-gray-900">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h3 class="text-2xl font-bold">Edit Invoice</h3>
                                <p class="text-gray-500">Bill to:</p>
                                <select name="patient_id" id="patient_id" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ $bill->patient_id == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500">Invoice # {{ $bill->bill_number }}</p>
                                <p class="text-gray-500">Date: {{ ($bill->bill_date ?? $bill->created_at)->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Bill Items Table -->
                        <div class="overflow-x-auto mb-8">
                            <table class="min-w-full divide-y divide-gray-200" id="bill-items">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service/Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bill->billItems as $index => $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select name="services[{{ $index }}][service_id]" class="service-select block w-full rounded-md border-gray-300 shadow-sm">
                                                <option value="">Select Service</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ $item->service_id == $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" name="services[{{ $index }}][quantity]" class="quantity-input w-20 rounded-md border-gray-300 shadow-sm" value="{{ $item->quantity }}" min="1">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap price-cell">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap total-cell">${{ number_format($item->total_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button type="button" class="remove-item text-red-500">Remove</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" id="add-item" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-lg">+ Add Item</button>
                        </div>

                        <!-- Totals -->
                        <div class="flex justify-end mb-8">
                            <div class="w-64">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span id="subtotal">${{ number_format($bill->subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tax (10%):</span>
                                        <span id="tax">${{ number_format($bill->tax_amount, 2) }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2">
                                        <div class="flex justify-between">
                                            <span class="text-lg font-semibold text-gray-900">Total:</span>
                                            <span id="total" class="text-lg font-bold text-gray-900">${{ number_format($bill->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" {{ $bill->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ $bill->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                                    <option value="insurance" {{ $bill->payment_method == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="online" {{ $bill->payment_method == 'online' ? 'selected' : '' }}>Online</option>
                                </select>
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $bill->due_date->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Additional notes or comments...">{{ $bill->notes }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('billing.index') }}" class="bg-gray-200 text-gray-800 py-2 px-4 rounded-lg mr-4">Cancel</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                Update Invoice
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const billItemsTable = document.getElementById('bill-items').getElementsByTagName('tbody')[0];
            const addItemButton = document.getElementById('add-item');
            const services = @json($services);
            let rowIndex = {{ $bill->billItems->count() }}; // Start from current count

            addItemButton.addEventListener('click', () => {
                const newRow = billItemsTable.insertRow();
                newRow.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <select name="services[${rowIndex}][service_id]" class="service-select block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select Service</option>
                            ${services.map(s => `<option value="${s.id}" data-price="${s.price}">${s.name}</option>`).join('')}
                        </select>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="number" name="services[${rowIndex}][quantity]" class="quantity-input w-20 rounded-md border-gray-300 shadow-sm" value="1" min="1">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap price-cell">$0.00</td>
                    <td class="px-6 py-4 whitespace-nowrap total-cell">$0.00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button type="button" class="remove-item text-red-500">Remove</button>
                    </td>
                `;
                rowIndex++;
            });

            billItemsTable.addEventListener('change', (e) => {
                if (e.target.classList.contains('service-select') || e.target.classList.contains('quantity-input')) {
                    updateRow(e.target.closest('tr'));
                }
            });

            billItemsTable.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-item')) {
                    e.target.closest('tr').remove();
                    updateTotals();
                }
            });

            function updateRow(row) {
                const serviceSelect = row.querySelector('.service-select');
                const quantityInput = row.querySelector('.quantity-input');
                const priceCell = row.querySelector('.price-cell');
                const totalCell = row.querySelector('.total-cell');

                const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const total = price * quantity;

                priceCell.textContent = `$${price.toFixed(2)}`;
                totalCell.textContent = `$${total.toFixed(2)}`;

                updateTotals();
            }

            function updateTotals() {
                const rows = billItemsTable.querySelectorAll('tr');
                let subtotal = 0;

                rows.forEach(row => {
                    const totalCell = row.querySelector('.total-cell');
                    if (totalCell) {
                        const totalText = totalCell.textContent;
                        subtotal += parseFloat(totalText.replace('$', '')) || 0;
                    }
                });

                const tax = subtotal * 0.10; // 10% tax
                const total = subtotal + tax;

                document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
                document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
                document.getElementById('total').textContent = `$${total.toFixed(2)}`;
            }

            // Initialize existing rows
            const existingRows = billItemsTable.querySelectorAll('tr');
            existingRows.forEach(row => {
                updateRow(row);
            });
        });
    </script>
</x-app-layout>
