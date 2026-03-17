<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hospital Information - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #map { height: 300px; width: 100%; border-radius: 0.5rem; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">🏥 Edit Hospital Information</h1>
                <p class="text-sm opacity-90">{{ auth()->user()->name }} - {{ $facility->name }}</p>
            </div>
            <a href="/admin" class="bg-gray-500 px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Back to Dashboard</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" action="{{ route('admin.facility.update') }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Basic Information</h3>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Hospital Name</label>
                            <input type="text" name="name" value="{{ old('name', $facility->name) }}" required 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $facility->phone) }}" required 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                            <textarea name="address" rows="3" required 
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $facility->address) }}</textarea>
                        </div>
                        
                        <h3 class="text-lg font-semibold mb-4 mt-6 border-b pb-2">Services Offered</h3>
                        
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="has_icu" value="1" {{ old('has_icu', $facility->has_icu) ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">ICU (Intensive Care Unit)</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="has_trauma" value="1" {{ old('has_trauma', $facility->has_trauma) ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">Trauma Center</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="has_maternity" value="1" {{ old('has_maternity', $facility->has_maternity) ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-700">Maternity Services</span>
                            </label>
                        </div>
                        
                        <div class="mb-4 mt-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Other Services</label>
                            <textarea name="other_services" rows="4" 
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('other_services', $facility->other_services) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Separate services with commas</p>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Location</h3>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Latitude</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $facility->latitude) }}" required 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Longitude</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $facility->longitude) }}" required 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <div id="map"></div>
                            <p class="text-xs text-gray-500 mt-1">Drag the marker to update coordinates</p>
                        </div>
                        
                        <div class="mb-4 bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-sm mb-2">📍 Current Location</h4>
                            <p class="text-sm">Latitude: <span id="current-lat">{{ $facility->latitude }}</span></p>
                            <p class="text-sm">Longitude: <span id="current-lng">{{ $facility->longitude }}</span></p>
                        </div>
                        
                        <h3 class="text-lg font-semibold mb-4 mt-6 border-b pb-2">Contact Person (Optional)</h3>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Contact Name</label>
                            <input type="text" name="contact_name" value="{{ old('contact_name') }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email') }}" 
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 border-t pt-6 mt-6">
                    <a href="/admin" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Hospital Information
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const lat = parseFloat(document.getElementById('latitude').value) || -20.1625;
        const lng = parseFloat(document.getElementById('longitude').value) || 28.5825;
        
        const map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        
        // Add draggable marker
        const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        
        // Update inputs when marker is dragged
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(6);
            document.getElementById('longitude').value = position.lng.toFixed(6);
            document.getElementById('current-lat').textContent = position.lat.toFixed(6);
            document.getElementById('current-lng').textContent = position.lng.toFixed(6);
        });
        
        // Also update when inputs change manually
        document.getElementById('latitude').addEventListener('change', updateMarkerFromInputs);
        document.getElementById('longitude').addEventListener('change', updateMarkerFromInputs);
        
        function updateMarkerFromInputs() {
            const newLat = parseFloat(document.getElementById('latitude').value);
            const newLng = parseFloat(document.getElementById('longitude').value);
            
            if (!isNaN(newLat) && !isNaN(newLng)) {
                marker.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng]);
                document.getElementById('current-lat').textContent = newLat.toFixed(6);
                document.getElementById('current-lng').textContent = newLng.toFixed(6);
            }
        }
    </script>
</body>
</html>