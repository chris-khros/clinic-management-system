<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white p-6 rounded-lg shadow mb-8 flex flex-wrap gap-4 items-center">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <button class="mt-6 bg-blue-600 text-white py-2 px-4 rounded-lg">Filter</button>
                <div class="ml-auto flex gap-4">
                    <button class="bg-green-600 text-white py-2 px-4 rounded-lg">Export CSV</button>
                    <button class="bg-purple-600 text-white py-2 px-4 rounded-lg">Email Report</button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Income Summary -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Income Summary</h3>
                    <canvas id="incomeChart"></canvas>
                </div>

                <!-- Patient Flow -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Patient Flow</h3>
                    <canvas id="patientFlowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Income Summary Chart
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            new Chart(incomeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Income',
                        data: [12000, 19000, 3000, 5000, 2000, 30000],
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                    }]
                }
            });

            // Patient Flow Chart
            const patientFlowCtx = document.getElementById('patientFlowChart').getContext('2d');
            new Chart(patientFlowCtx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    datasets: [{
                        label: 'Number of Patients',
                        data: [65, 59, 80, 81, 56, 55],
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                }
            });
        });
    </script>
</x-app-layout>

