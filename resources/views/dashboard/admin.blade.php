<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Staff</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $data['total_staff'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Patients</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $data['total_patients'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Today's Appointments</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $data['today_appointments'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                                <p class="text-2xl font-semibold text-gray-900">${{ number_format($data['total_revenue'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Patient Quick Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Quick Search</h3>
                    <div class="relative">
                        <input type="text"
                               id="dashboard-patient-search"
                               placeholder="Search by name, ID, phone, or email..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               autocomplete="off">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="dashboard-search-results" class="mt-2 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-96 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('staff.create') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="text-blue-600 font-semibold">Add Staff Member</div>
                            <div class="text-sm text-gray-600">Register new staff member</div>
                        </a>

                        <a href="{{ route('patients.create') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="text-green-600 font-semibold">Register Patient</div>
                            <div class="text-sm text-gray-600">Add new patient</div>
                        </a>

                        <a href="{{ route('appointments.create') }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="text-yellow-600 font-semibold">Schedule Appointment</div>
                            <div class="text-sm text-gray-600">Book new appointment</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reports and Analytics Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Reports & Analytics</h3>
                </div>
                <div class="p-6">
                    <!-- Report Navigation Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <button onclick="showReportSection('income')" id="income-tab" class="report-tab py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                                Income Summary
                            </button>
                            <button onclick="showReportSection('patient-flow')" id="patient-flow-tab" class="report-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Patient Flow
                            </button>
                        </nav>
                    </div>

                    <!-- Income Summary Report Section -->
                    <div id="income-section" class="report-section">
                        <!-- Filters -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Income Summary Filters</h4>
                            <form id="income-filters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="income_doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                                    <select name="doctor_id" id="income_doctor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">All Doctors</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="income_report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                                    <select name="report_type" id="income_report_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">All Time</option>
                                        <option value="daily">Today</option>
                                        <option value="weekly">This Week</option>
                                        <option value="monthly">This Month</option>
                                        <option value="yearly">This Year</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="income_date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                                    <input type="date" name="date_from" id="income_date_from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label for="income_date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                                    <input type="date" name="date_to" id="income_date_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="md:col-span-4">
                                    <button type="button" onclick="loadIncomeReport()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Load Report
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Income Summary Results -->
                        <div id="income-results" class="hidden">
                            <!-- Summary Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                                    <p class="text-xl font-semibold text-gray-900" id="total-revenue">$0.00</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Total Bills</p>
                                    <p class="text-xl font-semibold text-gray-900" id="total-bills">0</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Paid Bills</p>
                                    <p class="text-xl font-semibold text-gray-900" id="paid-bills">0</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Pending Bills</p>
                                    <p class="text-xl font-semibold text-gray-900" id="pending-bills">0</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Partial Bills</p>
                                    <p class="text-xl font-semibold text-gray-900" id="partial-bills">0</p>
                                </div>
                            </div>

                            <!-- Chart -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Revenue Trend</h4>
                                <div class="h-64">
                                    <canvas id="incomeChart"></canvas>
                                </div>
                            </div>

                            <!-- Export Actions -->
                            <div class="flex space-x-4 mb-6">
                                <button onclick="exportIncomeReport()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download CSV
                                </button>
                                <button onclick="showEmailModal('income')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email Report
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Flow Report Section -->
                    <div id="patient-flow-section" class="report-section hidden">
                        <!-- Filters -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Patient Flow Filters</h4>
                            <form id="patient-flow-filters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="flow_doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                                    <select name="doctor_id" id="flow_doctor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">All Doctors</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="flow_report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                                    <select name="report_type" id="flow_report_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">All Time</option>
                                        <option value="daily">Today</option>
                                        <option value="weekly">This Week</option>
                                        <option value="monthly">This Month</option>
                                        <option value="yearly">This Year</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="flow_date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                                    <input type="date" name="date_from" id="flow_date_from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label for="flow_date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                                    <input type="date" name="date_to" id="flow_date_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="md:col-span-4">
                                    <button type="button" onclick="loadPatientFlowReport()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Load Report
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Patient Flow Results -->
                        <div id="patient-flow-results" class="hidden">
                            <!-- Summary Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Total Appointments</p>
                                    <p class="text-xl font-semibold text-gray-900" id="total-appointments">0</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Completed</p>
                                    <p class="text-xl font-semibold text-gray-900" id="completed-appointments">0</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Pending</p>
                                    <p class="text-xl font-semibold text-gray-900" id="pending-appointments">0</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500">Cancelled</p>
                                    <p class="text-xl font-semibold text-gray-900" id="cancelled-appointments">0</p>
                                </div>
                            </div>

                            <!-- Charts -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-4">Daily Appointments</h4>
                                    <div class="h-64">
                                        <canvas id="dailyChart"></canvas>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-4">Monthly Trends</h4>
                                    <div class="h-64">
                                        <canvas id="monthlyChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Export Actions -->
                            <div class="flex space-x-4">
                                <button onclick="exportPatientFlowReport()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download CSV
                                </button>
                                <button onclick="showEmailModal('patient_flow')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Report</h3>
                <form id="emailForm">
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Enter email address" required>
                    </div>
                    <input type="hidden" id="report_type" name="report_type">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideEmailModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Send Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let incomeChart = null;
        let dailyChart = null;
        let monthlyChart = null;
        let currentReportType = 'income';

        // Load doctors for filters and initialize search
        document.addEventListener('DOMContentLoaded', function() {
            loadDoctors();
            initializeDashboardSearch();
        });

        function loadDoctors() {
            fetch('/dashboard/income-summary')
                .then(response => response.json())
                .then(data => {
                    const doctorSelects = ['income_doctor_id', 'flow_doctor_id'];
                    doctorSelects.forEach(selectId => {
                        const select = document.getElementById(selectId);
                        select.innerHTML = '<option value="">All Doctors</option>';
                        data.doctors.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = doctor.user.name;
                            select.appendChild(option);
                        });
                    });
                });
        }

        function initializeDashboardSearch() {
            const searchInput = document.getElementById('dashboard-patient-search');
            const searchResults = document.getElementById('dashboard-search-results');
            let searchTimeout;

            // Debounced search function
            function performDashboardSearch(query) {
                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                fetch(`/search/patients?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displayDashboardSearchResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }

            // Display search results
            function displayDashboardSearchResults(patients) {
                if (patients.length === 0) {
                    searchResults.innerHTML = '<div class="p-4 text-gray-500 text-center">No patients found</div>';
                } else {
                    searchResults.innerHTML = patients.map(patient => `
                        <a href="${patient.url}" class="block p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">${patient.full_name}</div>
                                    <div class="text-sm text-gray-500">ID: ${patient.patient_id} • ${patient.phone}</div>
                                    ${patient.email ? `<div class="text-sm text-gray-500">${patient.email}</div>` : ''}
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">${patient.gender}</div>
                                    ${patient.age ? `<div class="text-sm text-gray-500">${patient.age} years</div>` : ''}
                                </div>
                            </div>
                        </a>
                    `).join('');
                }
                searchResults.classList.remove('hidden');
            }

            // Event listeners
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performDashboardSearch(query);
                }, 300);
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            // Handle keyboard navigation
            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    searchResults.classList.add('hidden');
                    searchInput.blur();
                }
            });
        }

        function showReportSection(section) {
            // Hide all sections
            document.querySelectorAll('.report-section').forEach(section => {
                section.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.report-tab').forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected section
            document.getElementById(section + '-section').classList.remove('hidden');

            // Add active class to selected tab
            const activeTab = document.getElementById(section + '-tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-blue-500', 'text-blue-600');

            currentReportType = section;
        }

        function loadIncomeReport() {
            const form = document.getElementById('income-filters');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            fetch('/dashboard/income-summary?' + params)
                .then(response => response.json())
                .then(data => {
                    // Update summary cards
                    document.getElementById('total-revenue').textContent = '$' + data.totalRevenue.toFixed(2);
                    document.getElementById('total-bills').textContent = data.totalBills;
                    document.getElementById('paid-bills').textContent = data.paidBills;
                    document.getElementById('pending-bills').textContent = data.pendingBills;
                    document.getElementById('partial-bills').textContent = data.partialBills;

                    // Update chart
                    updateIncomeChart(data.dailyData);

                    // Show results
                    document.getElementById('income-results').classList.remove('hidden');
                });
        }

        function loadPatientFlowReport() {
            const form = document.getElementById('patient-flow-filters');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            fetch('/dashboard/patient-flow?' + params)
                .then(response => response.json())
                .then(data => {
                    // Update summary cards
                    document.getElementById('total-appointments').textContent = data.totalAppointments;
                    document.getElementById('completed-appointments').textContent = data.completedAppointments;
                    document.getElementById('pending-appointments').textContent = data.pendingAppointments;
                    document.getElementById('cancelled-appointments').textContent = data.cancelledAppointments;

                    // Update charts
                    updateDailyChart(data.dailyData);
                    updateMonthlyChart(data.monthlyTrends);

                    // Show results
                    document.getElementById('patient-flow-results').classList.remove('hidden');
                });
        }

        function updateIncomeChart(dailyData) {
            const ctx = document.getElementById('incomeChart').getContext('2d');

            if (incomeChart) {
                incomeChart.destroy();
            }

            incomeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dailyData.map(item => item.date),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: dailyData.map(item => item.revenue),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateDailyChart(dailyData) {
            const ctx = document.getElementById('dailyChart').getContext('2d');

            if (dailyChart) {
                dailyChart.destroy();
            }

            dailyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dailyData.map(item => item.date),
                    datasets: [{
                        label: 'Total Appointments',
                        data: dailyData.map(item => item.appointments),
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }, {
                        label: 'Completed',
                        data: dailyData.map(item => item.completed),
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    }, {
                        label: 'Pending',
                        data: dailyData.map(item => item.pending),
                        backgroundColor: 'rgba(245, 158, 11, 0.5)',
                        borderColor: 'rgb(245, 158, 11)',
                        borderWidth: 1
                    }, {
                        label: 'Cancelled',
                        data: dailyData.map(item => item.cancelled),
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function updateMonthlyChart(monthlyTrends) {
            const ctx = document.getElementById('monthlyChart').getContext('2d');

            if (monthlyChart) {
                monthlyChart.destroy();
            }

            monthlyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyTrends.map(item => item.month),
                    datasets: [{
                        label: 'Total Appointments',
                        data: monthlyTrends.map(item => item.appointments),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    }, {
                        label: 'Unique Patients',
                        data: monthlyTrends.map(item => item.unique_patients),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function exportIncomeReport() {
            const form = document.getElementById('income-filters');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            window.open('/dashboard/export/income-summary?' + params, '_blank');
        }

        function exportPatientFlowReport() {
            const form = document.getElementById('patient-flow-filters');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            window.open('/dashboard/export/patient-flow?' + params, '_blank');
        }

        function showEmailModal(reportType) {
            document.getElementById('report_type').value = reportType;
            document.getElementById('emailModal').classList.remove('hidden');
        }

        function hideEmailModal() {
            document.getElementById('emailModal').classList.add('hidden');
        }

        document.getElementById('emailForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/dashboard/email-report', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Report sent successfully!');
                    hideEmailModal();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the report');
            });
        });
    </script>
</x-app-layout>
