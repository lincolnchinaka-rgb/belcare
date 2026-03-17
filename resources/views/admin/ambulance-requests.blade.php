<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambulance Requests - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .status-pending { background-color: #f59e0b; color: white; }
        .status-dispatched { background-color: #3b82f6; color: white; }
        .status-en_route { background-color: #8b5cf6; color: white; }
        .status-arrived { background-color: #10b981; color: white; }
        .status-completed { background-color: #6b7280; color: white; }
        .status-cancelled { background-color: #ef4444; color: white; }
        .hospital-badge {
            background-color: #e5e7eb;
            color: #1f2937;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">🚑 Ambulance Requests</h1>
                <p class="text-sm opacity-90">
                    {{ auth()->user()->name }} - 
                    <span class="font-semibold">{{ App\Models\Facility::find(auth()->user()->facility_id)->name ?? 'Your Hospital' }}</span>
                </p>
            </div>
            <a href="/admin" class="bg-gray-500 px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Back to Dashboard</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <!-- Hospital Info Card -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Showing requests for:</p>
                    <p class="text-xl font-bold text-gray-800" id="hospital-name-display">
                        {{ App\Models\Facility::find(auth()->user()->facility_id)->name ?? 'Your Hospital' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="text-sm text-gray-500">Pending</div>
                <div class="text-2xl font-bold" id="stat-pending">0</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="text-sm text-gray-500">Dispatched</div>
                <div class="text-2xl font-bold" id="stat-dispatched">0</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="text-sm text-gray-500">En Route</div>
                <div class="text-2xl font-bold" id="stat-en_route">0</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="text-sm text-gray-500">Arrived</div>
                <div class="text-2xl font-bold" id="stat-arrived">0</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
                <div class="text-sm text-gray-500">Completed</div>
                <div class="text-2xl font-bold" id="stat-completed">0</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b">
                <nav class="flex -mb-px">
                    <button onclick="filterRequests('all')" class="tab-btn active px-6 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600" data-filter="all">All Requests</button>
                    <button onclick="filterRequests('pending')" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-filter="pending">Pending</button>
                    <button onclick="filterRequests('dispatched')" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-filter="dispatched">Dispatched</button>
                    <button onclick="filterRequests('en_route')" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-filter="en_route">En Route</button>
                    <button onclick="filterRequests('arrived')" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-filter="arrived">Arrived</button>
                </nav>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="requests-table-body" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading requests...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center" style="z-index: 9999;">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Update Request Status</h3>
            <div id="modal-content">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ============================================
        // GLOBAL VARIABLES
        // ============================================
        let currentFilter = 'all';
        let allRequests = [];
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ============================================
// LOAD REQUESTS - FIXED VERSION
// ============================================
function loadRequests() {
    console.log('Loading ambulance requests...');
    
    const tbody = document.getElementById('requests-table-body');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                </div>
                <p class="mt-2">Loading requests...</p>
            </td>
        </tr>
    `;

    fetch('/api/ambulance/requests', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        credentials: 'same-origin'
    })
    .then(res => {
        console.log('Response status:', res.status);
        if (res.status === 401) {
            // Session expired - show message but don't redirect automatically
            throw new Error('session_expired');
        }
        if (!res.ok) {
            throw new Error('Failed to load requests');
        }
        return res.json();
    })
    .then(data => {
        console.log('Data received:', data);
        if (data.success) {
            allRequests = data.data;
            updateStats();
            renderRequests();
        } else {
            showError(data.message || 'Failed to load requests');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.message === 'session_expired') {
            // Show session expired message but don't redirect
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center">
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">
                            <p class="font-bold">⚠️ Session Expired</p>
                            <p class="text-sm mb-3">Your session has expired. Please login again.</p>
                            <a href="/" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">
                                Go to Login
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            showError(error.message);
        }
    });
}
// ============================================
// SHOW ERROR MESSAGE
// ============================================
function showError(message) {
    const tbody = document.getElementById('requests-table-body');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-4 text-center text-red-500">
                ❌ ${message}
                <br>
                <button onclick="loadRequests()" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded text-sm hover:bg-blue-600">
                    Try Again
                </button>
            </td>
        </tr>
    `;
}
        // ============================================
        // SHOW ERROR MESSAGE
        // ============================================
        function showError(message) {
            const tbody = document.getElementById('requests-table-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-red-500">
                        ${message}
                    </td>
                </tr>
            `;
        }

        // ============================================
        // UPDATE STATISTICS
        // ============================================
        function updateStats() {
            const stats = {
                pending: allRequests.filter(r => r.status === 'pending').length,
                dispatched: allRequests.filter(r => r.status === 'dispatched').length,
                en_route: allRequests.filter(r => r.status === 'en_route').length,
                arrived: allRequests.filter(r => r.status === 'arrived').length,
                completed: allRequests.filter(r => r.status === 'completed').length
            };

            document.getElementById('stat-pending').textContent = stats.pending;
            document.getElementById('stat-dispatched').textContent = stats.dispatched;
            document.getElementById('stat-en_route').textContent = stats.en_route;
            document.getElementById('stat-arrived').textContent = stats.arrived;
            document.getElementById('stat-completed').textContent = stats.completed;
        }

        // ============================================
        // FILTER REQUESTS
        // ============================================
        function filterRequests(filter) {
            currentFilter = filter;
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.dataset.filter === filter) {
                    btn.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('text-gray-500');
                }
            });
            
            renderRequests();
        }

        // ============================================
        // RENDER REQUESTS TABLE
        // ============================================
        function renderRequests() {
            const tbody = document.getElementById('requests-table-body');
            
            let filtered = allRequests;
            if (currentFilter !== 'all') {
                filtered = allRequests.filter(r => r.status === currentFilter);
            }

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No requests found for your hospital</td></tr>`;
                return;
            }

            tbody.innerHTML = filtered.map(request => {
                const statusClass = `status-${request.status}`;
                const date = new Date(request.created_at).toLocaleString();
                
                return `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#${request.id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${request.patient_name || 'Unknown'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${request.patient_phone || 'N/A'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <button onclick="showMap(${request.pickup_latitude}, ${request.pickup_longitude})" class="text-blue-600 hover:text-blue-900">
                                View Location
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${request.status.replace('_', ' ').toUpperCase()}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="updateStatus(${request.id})" class="text-blue-600 hover:text-blue-900 mr-3">Update</button>
                            <button onclick="viewDetails(${request.id})" class="text-green-600 hover:text-green-900">View</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // ============================================
        // SHOW MAP MODAL
        // ============================================
        function showMap(lat, lng) {
            const modal = document.getElementById('statusModal');
            const content = document.getElementById('modal-content');
            
            content.innerHTML = `
                <div id="map" style="height: 300px; width: 100%;"></div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Close</button>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                const map = L.map('map').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker([lat, lng]).addTo(map).bindPopup('Pickup Location').openPopup();
            }, 100);
        }

        // ============================================
        // UPDATE STATUS
        // ============================================
        function updateStatus(requestId) {
            const request = allRequests.find(r => r.id === requestId);
            const modal = document.getElementById('statusModal');
            const content = document.getElementById('modal-content');
            
            const statuses = ['pending', 'dispatched', 'en_route', 'arrived', 'completed', 'cancelled'];
            
            content.innerHTML = `
                <p class="mb-4">Update status for request #${requestId}</p>
                <select id="new-status" class="w-full border rounded-lg px-3 py-2 mb-4">
                    ${statuses.map(s => `<option value="${s}" ${s === request.status ? 'selected' : ''}>${s.replace('_', ' ').toUpperCase()}</option>`).join('')}
                </select>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                    <button onclick="submitStatusUpdate(${requestId})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // ============================================
        // SUBMIT STATUS UPDATE
        // ============================================
        function submitStatusUpdate(requestId) {
            const newStatus = document.getElementById('new-status').value;
            
            fetch(`/api/ambulance/requests/${requestId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({ status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Status updated successfully!');
                    closeModal();
                    loadRequests();
                } else {
                    if (data.message === 'Unauthorized - This request belongs to another hospital') {
                        alert('❌ You cannot update requests from other hospitals!');
                    } else {
                        alert('❌ Failed to update status: ' + (data.message || 'Unknown error'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error updating status');
            });
        }

        // ============================================
        // VIEW DETAILS
        // ============================================
        function viewDetails(requestId) {
            const request = allRequests.find(r => r.id === requestId);
            const modal = document.getElementById('statusModal');
            const content = document.getElementById('modal-content');
            
            content.innerHTML = `
                <div class="space-y-3">
                    <p><strong>Request ID:</strong> #${request.id}</p>
                    <p><strong>Patient:</strong> ${request.patient_name || 'Unknown'}</p>
                    <p><strong>Contact:</strong> ${request.patient_phone || 'N/A'}</p>
                    <p><strong>Notes:</strong> ${request.notes || 'No notes'}</p>
                    <p><strong>Status:</strong> ${request.status.replace('_', ' ').toUpperCase()}</p>
                    <p><strong>Requested:</strong> ${new Date(request.created_at).toLocaleString()}</p>
                    ${request.dispatched_at ? `<p><strong>Dispatched:</strong> ${new Date(request.dispatched_at).toLocaleString()}</p>` : ''}
                    ${request.en_route_at ? `<p><strong>En Route:</strong> ${new Date(request.en_route_at).toLocaleString()}</p>` : ''}
                    ${request.arrived_at ? `<p><strong>Arrived:</strong> ${new Date(request.arrived_at).toLocaleString()}</p>` : ''}
                    ${request.completed_at ? `<p><strong>Completed:</strong> ${new Date(request.completed_at).toLocaleString()}</p>` : ''}
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Close</button>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // ============================================
        // CLOSE MODAL
        // ============================================
        function closeModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusModal').classList.remove('flex');
        }

        // ============================================
        // LOAD REQUESTS ON PAGE LOAD
        // ============================================
        document.addEventListener('DOMContentLoaded', loadRequests);
    </script>
</body>
</html>