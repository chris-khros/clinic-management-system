<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Invoice') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('billing.store') }}" method="POST">
                    @csrf
                    <div class="p-6 md:p-8 text-gray-900">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h3 class="text-2xl font-bold">New Invoice</h3>
                                <p class="text-gray-500">Bill to:</p>
                                <select name="patient_id" id="patient_id" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500">Invoice # <input type="text" value="{{ $new_bill_id }}" class="w-20 text-right" readonly></p>
                                <p class="text-gray-500">Date: {{ now()->format('M d, Y') }}</p>
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
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                            <button type="button" id="add-item" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-lg">+ Add Item</button>
                        </div>

                        <!-- Totals -->
                        <div class="flex justify-end mb-8">
                            <div class="w-full md:w-1/3">
                                <div class="flex justify-between py-2 border-b">
                                    <span class="font-medium">Subtotal</span>
                                    <span id="subtotal">$0.00</span>
                                </div>
                                <div class="flex justify-between py-2 border-b">
                                    <span class="font-medium">Tax (10%)</span>
                                    <span id="tax">$0.00</span>
                                </div>
                                <div class="flex justify-between py-2 font-bold text-xl">
                                    <span>Total</span>
                                    <span id="total">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div>
                                <label for="amount_paid" class="block text-sm font-medium text-gray-700">Amount Paid</label>
                                <input type="number" name="amount_paid" id="amount_paid" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="0.00" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end p-6 bg-gray-50">
                        <a href="{{ route('billing.index') }}" class="bg-gray-200 text-gray-800 py-2 px-4 rounded-lg mr-4">Cancel</a>
                        <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg">Generate Invoice</button>
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

            addItemButton.addEventListener('click', () => {
                const newRow = billItemsTable.insertRow();
                newRow.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <select name="items[][service_id]" class="service-select block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select Service</option>
                            ${services.map(s => `<option value="${s.id}" data-price="${s.price}">${s.name}</option>`).join('')}
                        </select>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="number" name="items[][quantity]" class="quantity-input w-20 rounded-md border-gray-300 shadow-sm" value="1" min="1">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap price-cell">$0.00</td>
                    <td class="px-6 py-4 whitespace-nowrap total-cell">$0.00</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <button type="button" class="remove-item text-red-500">Remove</button>
                    </td>
                `;
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
                const price = parseFloat(selectedOption.dataset.price) || 0;
                const quantity = parseInt(quantityInput.value) || 1;
                const total = price * quantity;

                priceCell.textContent = `${price.toFixed(2)}`;
                totalCell.textContent = `${total.toFixed(2)}`;
                updateTotals();
            }

            function updateTotals() {
                let subtotal = 0;
                document.querySelectorAll('#bill-items tbody tr').forEach(row => {
                    const totalText = row.querySelector('.total-cell').textContent;
                    subtotal += parseFloat(totalText.replace('
, '')) || 0;
                });

                const tax = subtotal * 0.10;
                const total = subtotal + tax;

                document.getElementById('subtotal').textContent = `${subtotal.toFixed(2)}`;
                document.getElementById('tax').textContent = `${tax.toFixed(2)}`;
                document.getElementById('total').textContent = `${total.toFixed(2)}`;
            }
        });
    </script>
</x-app-layout>
