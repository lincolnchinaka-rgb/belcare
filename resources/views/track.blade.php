<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Ambulance - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="bg-gray-100">
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">🚑 Track Your Ambulance</h1>
        </div>
    </div>

    <div class="container mx-auto p-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Request #<span id="request-id">Loading...</span></h2>
                    <span id="status-badge" class="px-3 py-1 rounded-full text-white text-sm font-semibold bg-yellow-500">Loading...</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Patient</p>
                        <p id="patient-name" class="font-semibold">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Contact</p>
                        <p id="patient-phone" class="font-semibold">-</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-500 text-sm">Hospital</p>
                    <p id="hospital-name" class="font-semibold">-</p>
                </div>

                <div class="border-t pt-4">
                    <p class="text-gray-500 text-sm mb-2">Timeline</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Requested:</span>
                            <span id="time-created" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dispatched:</span>
                            <span id="time-dispatched" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>En Route:</span>
                            <span id="time-en_route" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Arrived:</span>
                            <span id="time-arrived" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Completed:</span>
                            <span id="time-completed" class="font-medium">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="font-bold text-lg mb-4">Pickup Location</h3>
                <div id="map" style="height: 300px;" class="rounded-lg"></div>
            </div>

            <div class="mt-4 text-center">
                <a href="/" class="text-blue-600 hover:text-blue-800">← Back to Home</a>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const requestId = window.location.pathname.split('/').pop();
        let map = null;
        let marker = null;

        function loadRequest() {
            fetch(`/api/ambulance/location/${requestId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        updatePage(data.data);
                    }
                })
                .catch(() => {});
        }

        function updatePage(data) {
            document.getElementById('request-id').textContent = requestId;
            document.getElementById('patient-name').textContent = data.patient_name || 'Not provided';
            document.getElementById('patient-phone').textContent = data.patient_phone || 'Not provided';
            document.getElementById('hospital-name').textContent = data.facility?.name || 'Unknown';
            
            const status = data.status || 'pending';
            const badge = document.getElementById('status-badge');
            badge.textContent = status.replace('_', ' ').toUpperCase();
            
            const colors = {
                'pending': 'bg-yellow-500',
                'dispatched': 'bg-blue-500',
                'en_route': 'bg-purple-500',
                'arrived': 'bg-green-500',
                'completed': 'bg-gray-500',
                'cancelled': 'bg-red-500'
            };
            badge.className = `px-3 py-1 rounded-full text-white text-sm font-semibold ${colors[status] || 'bg-gray-500'}`;
            
            if (data.timestamps) {
                document.getElementById('time-created').textContent = data.timestamps.created_at || '-';
                document.getElementById('time-dispatched').textContent = data.timestamps.dispatched_at || '-';
                document.getElementById('time-en_route').textContent = data.timestamps.en_route_at || '-';
                document.getElementById('time-arrived').textContent = data.timestamps.arrived_at || '-';
                document.getElementById('time-completed').textContent = data.timestamps.completed_at || '-';
            }
            
            if (data.pickup_latitude && data.pickup_longitude && !map) {
                map = L.map('map').setView([data.pickup_latitude, data.pickup_longitude], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                marker = L.marker([data.pickup_latitude, data.pickup_longitude]).addTo(map)
                    .bindPopup('Pickup Location').openPopup();
            }
        }

        loadRequest();
        setInterval(loadRequest, 10000);
    </script>
</body>
</html>