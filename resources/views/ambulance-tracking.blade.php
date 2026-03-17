<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Ambulance Tracking - BEL-CARE</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #map { height: 70vh; width: 100%; }
        .user-marker { font-size: 30px; }
        .hospital-marker { font-size: 30px; }
        .ambulance-marker { 
            font-size: 36px; 
            filter: drop-shadow(0 0 8px #ef4444);
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">🚑 Live Ambulance Tracking</h1>
            <p>Request #{{ $request->id }} - {{ $request->facility->name }}</p>
        </div>
    </div>

    <div class="container mx-auto p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold">Status</h3>
                <p id="status" class="text-lg mt-1">
                    <span class="px-3 py-1 rounded-full text-sm 
                        @if($request->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($request->status == 'dispatched') bg-blue-100 text-blue-800
                        @elseif($request->status == 'en_route') bg-purple-100 text-purple-800
                        @elseif($request->status == 'arrived') bg-green-100 text-green-800
                        @elseif($request->status == 'completed') bg-green-100 text-green-800
                        @else bg-gray-100 @endif
                    ">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold">Estimated Arrival</h3>
                <p id="eta" class="text-lg mt-1">
                    <span id="eta-value">{{ $request->eta_minutes ? $request->eta_minutes . ' minutes' : 'Calculating...' }}</span>
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-semibold">Hospital</h3>
                <p class="text-lg mt-1">{{ $request->facility->name }}</p>
                <p class="text-sm text-gray-500">{{ $request->facility->phone }}</p>
            </div>
        </div>

        <div id="map" class="rounded-lg shadow mb-4"></div>

        <div class="flex gap-4 text-sm text-gray-600 bg-white p-3 rounded-lg shadow">
            <div class="flex items-center"><span class="mr-1 text-2xl">📍</span> Your location</div>
            <div class="flex items-center"><span class="mr-1 text-2xl">🏥</span> Hospital</div>
            <div class="flex items-center"><span class="mr-1 text-2xl">🚑</span> Ambulance</div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([{{ $request->latitude }}, {{ $request->longitude }}], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const userIcon = L.divIcon({ className: 'user-marker', html: '📍', iconSize: [30, 30] });
        const hospitalIcon = L.divIcon({ className: 'hospital-marker', html: '🏥', iconSize: [30, 30] });
        const ambulanceIcon = L.divIcon({ className: 'ambulance-marker', html: '🚑', iconSize: [40, 40] });

        const userMarker = L.marker([{{ $request->latitude }}, {{ $request->longitude }}], {icon: userIcon})
            .addTo(map).bindPopup('Your location');

        const hospitalMarker = L.marker([{{ $request->facility->latitude }}, {{ $request->facility->longitude }}], {icon: hospitalIcon})
            .addTo(map).bindPopup('{{ $request->facility->name }}');

        let ambulanceMarker = null;
        @if($request->ambulance_lat && $request->ambulance_lng)
            ambulanceMarker = L.marker([{{ $request->ambulance_lat }}, {{ $request->ambulance_lng }}], {icon: ambulanceIcon})
                .addTo(map).bindPopup('Ambulance');
        @endif

        const bounds = L.latLngBounds([
            [{{ $request->latitude }}, {{ $request->longitude }}],
            [{{ $request->facility->latitude }}, {{ $request->facility->longitude }}]
        ]);
        @if($request->ambulance_lat && $request->ambulance_lng)
            bounds.extend([{{ $request->ambulance_lat }}, {{ $request->ambulance_lng }}]);
        @endif
        map.fitBounds(bounds.pad(0.2));

        setInterval(() => {
            fetch('/api/ambulance/location/{{ $request->id }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('status').innerHTML = 
                        `<span class="px-3 py-1 rounded-full text-sm ${getStatusClass(data.status)}">${data.status}</span>`;
                    document.getElementById('eta-value').innerText = data.eta_minutes ? data.eta_minutes + ' minutes' : 'Calculating...';

                    if (data.ambulance_lat && data.ambulance_lng) {
                        if (ambulanceMarker) {
                            ambulanceMarker.setLatLng([data.ambulance_lat, data.ambulance_lng]);
                        } else {
                            ambulanceMarker = L.marker([data.ambulance_lat, data.ambulance_lng], {icon: ambulanceIcon})
                                .addTo(map).bindPopup('Ambulance');
                        }
                    }
                })
                .catch(err => console.error('Polling error:', err));
        }, 3000);

        function getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'dispatched': 'bg-blue-100 text-blue-800',
                'en_route': 'bg-purple-100 text-purple-800',
                'arrived': 'bg-green-100 text-green-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100';
        }
    </script>
</body>
</html>