<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">🚑 BEL-CARE Admin Dashboard</h1>
                <p class="text-sm opacity-90">Bulawayo Emergency Healthcare Locator</p>
            </div>
            <a href="/logout" class="bg-red-500 px-4 py-2 rounded-lg hover:bg-red-600 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <!-- Welcome Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome, {{ $user->name }}!</h2>
            <p class="text-gray-600">{{ $user->email }}</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm uppercase">Total Hospitals</p>
                        <p class="text-3xl font-bold text-gray-800" id="hospital-count">Loading...</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm uppercase">Ambulance Requests</p>
                        <p class="text-3xl font-bold text-gray-800" id="ambulance-count">0</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm uppercase">Resources Updated</p>
                        <p class="text-3xl font-bold text-gray-800" id="resources-count">Loading...</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
       <!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="/admin/resources" class="bg-blue-500 text-white p-4 rounded-lg hover:bg-blue-600 transition text-center">
            <div class="text-2xl mb-2">🏥</div>
            <div class="font-semibold">Update Hospital Resources</div>
            <div class="text-sm opacity-90">Beds, Staff, Blood, Equipment</div>
        </a>
        
        <a href="/admin/ambulance-requests" class="bg-green-500 text-white p-4 rounded-lg hover:bg-green-600 transition text-center">
            <div class="text-2xl mb-2">🚑</div>
            <div class="font-semibold">View Ambulance Requests</div>
            <div class="text-sm opacity-90">Manage incoming requests</div>
        </a>
        
        <a href="/admin/facility/edit" class="bg-purple-500 text-white p-4 rounded-lg hover:bg-purple-600 transition text-center">
            <div class="text-2xl mb-2">⚙️</div>
            <div class="font-semibold">Hospital Settings</div>
            <div class="text-sm opacity-90">Update hospital information</div>
        </a>
        
        <!-- Profile Settings Button -->
        <a href="/admin/profile" class="bg-gray-600 text-white p-4 rounded-lg hover:bg-gray-700 transition text-center">
            <div class="text-2xl mb-2">👤</div>
            <div class="font-semibold">Profile Settings</div>
            <div class="text-sm opacity-90">Change email & password</div>
        </a>
    </div>
</div>
        
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Activity</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Time</th>
                            <th class="px-4 py-2 text-left">Action</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="activity-log">
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                Loading recent activity...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Load hospital count
        fetch('/api/facilities')
            .then(res => res.json())
            .then(data => {
                document.getElementById('hospital-count').textContent = data.data.length;
                
                // Count resources updated
                const updated = data.data.filter(h => h.resources).length;
                document.getElementById('resources-count').textContent = updated + '/' + data.data.length;
                
                // Simple activity log
                const tbody = document.getElementById('activity-log');
                tbody.innerHTML = '';
                
                // Show last 5 hospitals with recent updates
                const recent = data.data
                    .filter(h => h.last_updated_at)
                    .sort((a, b) => new Date(b.last_updated_at) - new Date(a.last_updated_at))
                    .slice(0, 5);
                
                if (recent.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">No recent activity</td></tr>';
                } else {
                    recent.forEach(h => {
                        const date = new Date(h.last_updated_at).toLocaleString();
                        tbody.innerHTML += `
                            <tr class="border-t">
                                <td class="px-4 py-2">${date}</td>
                                <td class="px-4 py-2">${h.name} updated</td>
                                <td class="px-4 py-2"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Completed</span></td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(error => {
                document.getElementById('hospital-count').textContent = 'Error';
                document.getElementById('resources-count').textContent = 'Error';
            });
    </script>
</body>
</html>