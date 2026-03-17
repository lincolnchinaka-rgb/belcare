<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEL-CARE MedAccess Navigator - Bulawayo</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #map { height: calc(100vh - 200px); width: 100%; }
        .hospital-marker {
            background-color: #ef4444;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
        }
        .user-marker {
            font-size: 24px;
            filter: drop-shadow(0 0 6px #3b82f6);
        }
        .leaflet-routing-container {
            display: none !important;
        }
        .leaflet-popup-content {
            margin: 10px !important;
            line-height: 1.4 !important;
            max-height: 400px !important;
            overflow-y: auto !important;
            width: 300px !important;
        }
        .leaflet-popup-content button, .leaflet-popup-content a {
            pointer-events: auto !important;
        }
        .timestamp {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.5rem;
            border-top: 1px dashed #e5e7eb;
            padding-top: 0.5rem;
        }
        .audio-controls {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        .audio-btn {
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            cursor: pointer;
            border: none;
        }
        .speak-btn { background-color: #10b981; }
        .speak-btn:hover { background-color: #059669; }
        .stop-btn { background-color: #ef4444; }
        .stop-btn:hover { background-color: #dc2626; }
        .mute-btn { background-color: #6b7280; }
        .mute-btn:hover { background-color: #4b5563; }
        .unmute-btn { background-color: #10b981; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Login Button -->
    <div class="fixed top-4 right-4 z-[1000]">
        <button onclick="openLoginModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Admin Login
        </button>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center" style="z-index: 9999;">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Hospital Admin Login</h2>
                <button onclick="closeLoginModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="loginForm" onsubmit="handleLogin(event)">
                @csrf
                <input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="hospital@belcare.com" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" placeholder="password123" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div id="loginError" class="mb-4 text-red-500 text-sm hidden"></div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    Login
                </button>
            </form>
            <div class="mt-4 text-center text-sm text-gray-600">
                Demo: hospital@belcare.com / password123
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold">🚑 BEL-CARE MedAccess Navigator</h1>
            <p class="text-lg">Bulawayo Emergency Healthcare Locator</p>
        </div>
    </div>

    <div class="container mx-auto p-4">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="font-semibold text-lg mb-3">Filter by Services:</h2>
            <div class="flex flex-wrap gap-4">
                <label class="inline-flex items-center bg-blue-50 px-4 py-2 rounded-lg cursor-pointer">
                    <input type="checkbox" class="service-filter mr-2" value="icu" checked> ICU
                </label>
                <label class="inline-flex items-center bg-red-50 px-4 py-2 rounded-lg cursor-pointer">
                    <input type="checkbox" class="service-filter mr-2" value="trauma" checked> Trauma
                </label>
                <label class="inline-flex items-center bg-purple-50 px-4 py-2 rounded-lg cursor-pointer">
                    <input type="checkbox" class="service-filter mr-2" value="maternity" checked> Maternity
                </label>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="font-semibold text-lg mb-3">🚑 Find Nearest Hospital</h2>
            <div class="flex gap-2">
                <input type="text" id="search-input" placeholder="Search: hospital name or service (e.g., Mpilo, accident, malaria, farmasy)" class="flex-1 border rounded-lg px-4 py-2">
                <button id="search-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Search
                </button>
            </div>
            <div id="search-result" class="mt-2 text-sm text-gray-600"></div>
        </div>

        <!-- Map -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div id="map"></div>
        </div>

        <!-- Loading indicator -->
        <div id="loading" class="text-center py-8 hidden">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
            <p class="mt-2 text-gray-600">Loading hospitals...</p>
        </div>
    </div>

    <!-- Audio Controls -->
    <div class="audio-controls">
        <button id="speak-btn" class="audio-btn speak-btn">🔊 Speak Directions</button>
        <button id="stop-btn" class="audio-btn stop-btn">🔇 Stop</button>
        <button id="mute-btn" class="audio-btn mute-btn">🔇 Mute Audio</button>
    </div>

    <!-- Clear Route Button -->
    <button id="clear-route-btn" onclick="clearRoute()" class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-[1000] hover:bg-red-600 hidden">
        🗺️ Clear Route
    </button>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    
    <script>
        // Initialize map
        const map = L.map('map').setView([-20.1625, 28.5825], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Global variables
        let allHospitals = [];
        let markers = [];
        let userMarker = null;
        let routingControl = null;
        let currentUserLat = null;
        let currentUserLng = null;
        let audioEnabled = true;
        let currentRoute = null;
        let currentHospital = null;
        let speechTimers = [];

        const loadingEl = document.getElementById('loading');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const clearRouteBtn = document.getElementById('clear-route-btn');
        
        // Audio controls
        const speakBtn = document.getElementById('speak-btn');
        const stopBtn = document.getElementById('stop-btn');
        const muteBtn = document.getElementById('mute-btn');

        speakBtn.addEventListener('click', function() {
            if (currentRoute && currentHospital) {
                speakDirections(currentRoute, currentHospital.name);
            } else {
                alert('Please search for a hospital first');
            }
        });
        
        stopBtn.addEventListener('click', function() {
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
                speechTimers.forEach(timer => clearTimeout(timer));
                speechTimers = [];
            }
        });
        
        muteBtn.addEventListener('click', function() {
            audioEnabled = !audioEnabled;
            this.innerHTML = audioEnabled ? '🔇 Mute Audio' : '🔊 Unmute Audio';
            this.className = `audio-btn ${audioEnabled ? 'mute-btn' : 'unmute-btn'}`;
            
            if (!audioEnabled && window.speechSynthesis) {
                window.speechSynthesis.cancel();
                speechTimers.forEach(timer => clearTimeout(timer));
                speechTimers = [];
            }
        });

        const hospitalIcon = L.divIcon({
            className: 'hospital-marker',
            html: '🏥',
            iconSize: [30, 30]
        });

        // Login functions
        window.openLoginModal = () => document.getElementById('loginModal').classList.remove('hidden');
        window.closeLoginModal = () => document.getElementById('loginModal').classList.add('hidden');

        window.handleLogin = async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('loginError');
            const csrfToken = document.getElementById('csrf-token').value;
            
            errorDiv.classList.remove('hidden');
            errorDiv.innerHTML = '⏳ Logging in...';
            errorDiv.style.color = 'blue';

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('password', password);
                
                const res = await fetch('/simple-login', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });
                
                const data = await res.json();
                
                if (data.success) {
                    errorDiv.innerHTML = '✅ Success! Redirecting...';
                    errorDiv.style.color = 'green';
                    setTimeout(() => {
                        window.location.href = '/admin';
                    }, 1000);
                } else {
                    errorDiv.innerHTML = '❌ ' + (data.message || 'Invalid credentials');
                    errorDiv.style.color = 'red';
                }
            } catch (error) {
                console.error('Login error:', error);
                errorDiv.innerHTML = '❌ Connection error. Please try again.';
                errorDiv.style.color = 'red';
            }
        };

        // Load hospitals
        function loadHospitals() {
            loadingEl.classList.remove('hidden');
            
            fetch('/api/facilities?_=' + new Date().getTime())
                .then(res => res.json())
                .then(result => {
                    loadingEl.classList.add('hidden');
                    allHospitals = result.data;
                    displayHospitals(allHospitals);
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingEl.classList.add('hidden');
                });
        }

        // Calculate distance
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Display hospitals as markers
        function displayHospitals(hospitals) {
            markers.forEach(m => map.removeLayer(m));
            markers = [];

            if (hospitals.length === 0) return;

            hospitals.forEach(hospital => {
                const r = hospital.resources || {};

                const hospitalUpdated = hospital.last_updated_at 
                    ? new Date(hospital.last_updated_at).toLocaleString() 
                    : 'Never';
                const resourcesUpdated = hospital.resources?.last_updated_at 
                    ? new Date(hospital.resources.last_updated_at).toLocaleString() 
                    : 'Never';
                
                let html = `
                    <div class="text-center" style="max-width:300px; padding:5px;">
                        <h3 style="font-size:1.2rem; font-weight:bold; color:#1e40af;">${hospital.name}</h3>
                        <p style="margin:5px 0;">📞 <a href="tel:${hospital.phone}" style="color:#2563eb;">${hospital.phone}</a></p>
                `;

                if (r.icu_beds_total || r.general_beds_total || r.maternity_beds_total || r.pediatric_beds_total) {
                    html += `<hr style="margin:10px 0;"><p style="font-weight:bold; color:#2563eb;">🏥 BEDS AVAILABLE</p>`;
                    if (r.icu_beds_total) html += `<p>ICU: ${r.icu_beds_available}/${r.icu_beds_total}</p>`;
                    if (r.general_beds_total) html += `<p>General: ${r.general_beds_available}/${r.general_beds_total}</p>`;
                    if (r.maternity_beds_total) html += `<p>Maternity: ${r.maternity_beds_available}/${r.maternity_beds_total}</p>`;
                    if (r.pediatric_beds_total) html += `<p>Pediatric: ${r.pediatric_beds_available}/${r.pediatric_beds_total}</p>`;
                }

                if (r.doctors_on_duty || r.nurses_on_duty || r.paramedics_on_duty) {
                    html += `<hr style="margin:10px 0;"><p style="font-weight:bold; color:#059669;">👨‍⚕️ STAFF ON DUTY</p>`;
                    html += `<p>Doctors: ${r.doctors_on_duty || 0} | Nurses: ${r.nurses_on_duty || 0}</p>`;
                    if (r.paramedics_on_duty) html += `<p>Paramedics: ${r.paramedics_on_duty}</p>`;
                }

                if (r.ambulance_count) {
                    html += `<hr style="margin:10px 0;"><p style="font-weight:bold; color:#7e22ce;">🚑 AMBULANCES</p>`;
                    html += `<p>Available: ${r.ambulances_available ? '✅ Yes' : '❌ No'} (${r.ambulance_count} vehicles)</p>`;
                }

                if (r.ventilator_count) {
                    html += `<hr style="margin:10px 0;"><p style="font-weight:bold; color:#b45309;">⚡ EQUIPMENT</p>`;
                    html += `<p>Ventilators: ${r.ventilators_available ? '✅' : '❌'} (${r.ventilator_count} units)</p>`;
                }

                html += `<hr style="margin:10px 0;"><p style="font-weight:bold; color:#b91c1c;">🚨 EMERGENCY SERVICES</p>`;
                html += `<p>ICU: ${hospital.has_icu ? '✅' : '❌'} | Trauma: ${hospital.has_trauma ? '✅' : '❌'} | Maternity: ${hospital.has_maternity ? '✅' : '❌'}</p>`;

                if (hospital.other_services) {
                    html += `<hr style="margin:10px 0;"><p style="font-weight:bold;">📋 OTHER SERVICES</p>`;
                    html += `<p style="font-size:0.9rem;">${hospital.other_services}</p>`;
                }

                html += `
                    <hr style="margin:10px 0;">
                    <div style="display:flex; gap:10px; justify-content:center;">
                        <a href="tel:${hospital.phone}" class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 flex-1 text-center font-medium">
                            📞 Call
                        </a>
                        <button class="ambulance-btn bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 flex-1 text-center font-medium"
                                data-facility-id="${hospital.id}" data-facility-name="${hospital.name}">
                            🚑 Request Ambulance
                        </button>
                    </div>
                    <div class="timestamp">
                        <p>🏥 Info updated: ${hospitalUpdated}</p>
                        <p>📊 Resources updated: ${resourcesUpdated}</p>
                        <p>📍 ${hospital.address}</p>
                    </div>
                </div>`;

                const popupContent = document.createElement('div');
                popupContent.innerHTML = html;

                const marker = L.marker([parseFloat(hospital.latitude), parseFloat(hospital.longitude)], {icon: hospitalIcon})
                    .addTo(map)
                    .bindPopup(popupContent);
                markers.push(marker);
            });

            if (markers.length) {
                map.fitBounds(L.featureGroup(markers).getBounds().pad(0.1));
            }
        }

        // ============================================
        // AMBULANCE REQUEST
        // ============================================
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('ambulance-btn')) {
                e.preventDefault();
                const btn = e.target;
                const facilityId = btn.dataset.facilityId;
                
                (async () => {
                    try {
                        if (!currentUserLat || !currentUserLng) {
                            await getUserLocation();
                        }
                        
                        const name = prompt('Patient name (optional):');
                        if (name === null) return;
                        
                        const phone = prompt('Contact phone (optional):');
                        if (phone === null) return;
                        
                        const notes = prompt('Additional notes (optional):');
                        if (notes === null) return;
                        
                        const response = await fetch('/api/ambulance/request', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': csrfToken 
                            },
                            body: JSON.stringify({
                                facility_id: facilityId,
                                latitude: currentUserLat,
                                longitude: currentUserLng,
                                patient_name: name,
                                patient_phone: phone,
                                notes: notes
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            const trackUrl = `/track/${data.request.id}`;
                            if (confirm(`🚑 Ambulance requested!\nRequest ID: ${data.request.id}\n\nTrack now?`)) {
                                window.open(trackUrl, '_blank');
                            }
                        } else {
                            alert('Error: ' + (data.message || 'Failed to send request'));
                        }
                    } catch (error) {
                        console.error('Ambulance request error:', error);
                        alert('Failed to send request. Please try again.');
                    }
                })();
            }
        });

        // Filters
        document.querySelectorAll('.service-filter').forEach(cb => {
            cb.addEventListener('change', () => {
                const selected = Array.from(document.querySelectorAll('.service-filter:checked')).map(c => c.value);
                const filtered = allHospitals.filter(h => selected.every(s => h[`has_${s}`]));
                displayHospitals(filtered);
            });
        });

        // Get user location
        function getUserLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject('Geolocation not supported');
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    position => {
                        currentUserLat = position.coords.latitude;
                        currentUserLng = position.coords.longitude;
                        
                        if (userMarker) map.removeLayer(userMarker);
                        userMarker = L.marker([currentUserLat, currentUserLng], {
                            icon: L.divIcon({ className: 'user-marker', html: '📍' })
                        }).addTo(map).bindPopup('You are here').openPopup();
                        
                        displayHospitals(allHospitals);
                        resolve();
                    },
                    error => reject('Location permission denied'),
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });
        }

        // ============================================
        // COMPREHENSIVE MISSPELLING DICTIONARY
        // ============================================
        const searchDictionary = {
            // Hospital names
            'mpilo': 'Mpilo Central Hospital',
            'ubh': 'United Bulawayo Hospitals',
            'united bulawayo': 'United Bulawayo Hospitals',
            'mater dei': 'Mater Dei Hospital',
            'materdei': 'Mater Dei Hospital',
            'nkulumane': 'Nkulumane Clinic',
            'thorngrove': 'Thorngrove Infectious Diseases Hospital',
            'thorngrove infectious': 'Thorngrove Infectious Diseases Hospital',
            'pelandaba': 'Pelandaba Clinic',
            'hillside': 'Hillside Clinic',
            'royal women': 'Royal Women’s Clinic',
            'premier': 'Premier Hillside Hospital',
            'ingutsheni': 'Ingutsheni Central Hospital',
            'st luke': 'St. Luke\'s Hospital',
            'st lukes': 'St. Luke\'s Hospital',
            'bulawayo central': 'Bulawayo Central Hospital',
            'central': 'Bulawayo Central Hospital',
            'ekhuselweni': 'Ekhuselweni Clinic',
            'nketa': 'Nketa 8 Clinic',
            'emganwini': 'Emganwini Clinic',
            'mahatshula': 'Mahatshula Clinic',
            
            // ICU misspellings
            'icu': 'icu',
            'icyou': 'icu',
            'ickyou': 'icu',
            'ic u': 'icu',
            'i c u': 'icu',
            'intensive': 'icu',
            'intencive': 'icu',
            'critical': 'icu',
            'critikal': 'icu',
            
            // Trauma misspellings
            'trauma': 'trauma',
            'trama': 'trauma',
            'trouma': 'trauma',
            'tramua': 'trauma',
            'tromma': 'trauma',
            'traum': 'trauma',
            'accident': 'trauma',
            'aksident': 'trauma',
            'injury': 'trauma',
            'injure': 'trauma',
            'emergency': 'trauma',
            'emerjency': 'trauma',
            'bleeding': 'trauma',
            'bleed': 'trauma',
            'fracture': 'trauma',
            'fractur': 'trauma',
            'broken': 'trauma',
            'cut': 'trauma',
            'wound': 'trauma',
            
            // Maternity misspellings
            'maternity': 'maternity',
            'maturnity': 'maternity',
            'maternety': 'maternity',
            'maternaty': 'maternity',
            'materity': 'maternity',
            'matern': 'maternity',
            'pregnancy': 'maternity',
            'pregnency': 'maternity',
            'pregenancy': 'maternity',
            'pregant': 'maternity',
            'pregent': 'maternity',
            'pregnant': 'maternity',
            'labour': 'maternity',
            'labor': 'maternity',
            'baby': 'maternity',
            'babies': 'maternity',
            'childbirth': 'maternity',
            'delivery': 'maternity',
            'antenatal': 'maternity',
            'postnatal': 'maternity',
            
            // ============================================
            // UPDATED SERVICES - WITH MISSPELLINGS
            // ============================================
            'malaria': 'malaria',
            'maleria': 'malaria',
            'milaria': 'malaria',
            'malearia': 'malaria',
            
            'fever': 'fever',
            'feaver': 'fever',
            'fevre': 'fever',
            'high temperature': 'fever',
            'hot': 'fever',
            
            'pharmacy': 'pharmacy',
            'farmasy': 'pharmacy',
            'pharmasy': 'pharmacy',
            'farmacy': 'pharmacy',
            'drugs': 'pharmacy',
            'medicine': 'pharmacy',
            'meds': 'pharmacy',
            'chemist': 'pharmacy',
            
            'xray': 'xray',
            'x-ray': 'xray',
            'exray': 'xray',
            'xray': 'xray',
            'x ray': 'xray',
            'radiology': 'xray',
            
            'lab': 'lab',
            'laboratory': 'lab',
            'laba': 'lab',
            'blood test': 'lab',
            
            'blood': 'blood',
            'blud': 'blood',
            'blood bank': 'blood',
            'transfusion': 'blood',
            
            'vaccination': 'vaccination',
            'vacination': 'vaccination',
            'vaccine': 'vaccination',
            'vacin': 'vaccination',
            'immunization': 'vaccination',
            'immunisation': 'vaccination',
            
            'covid': 'covid',
            'covid-19': 'covid',
            'corona': 'covid',
            'coronavirus': 'covid',
            
            'hiv': 'hiv',
            'aids': 'hiv',
            
            'tb': 'tb',
            'tuberculosis': 'tb',
            't b': 'tb',
            
            'dental': 'dental',
            'dentist': 'dental',
            'teeth': 'dental',
            'tooth': 'dental',
            
            'eye': 'eye',
            'optical': 'eye',
            'vision': 'eye',
            'glasses': 'eye',
            
            'physio': 'physio',
            'physiotherapy': 'physio',
            'rehab': 'physio',
            'rehabilitation': 'physio',
            
            'cancer': 'cancer',
            'oncology': 'cancer',
            
            'screening': 'screening',
            'checkup': 'screening',
            'check up': 'screening',
            
            'ultrasound': 'ultrasound',
            'sonar': 'ultrasound',
            'scan': 'ultrasound',
            
            'mri': 'mri',
            'ct scan': 'ct',
            'cat scan': 'ct',
            
            'surgery': 'surgery',
            'operation': 'surgery',
            'theatre': 'surgery'
        };

        // ============================================
        // SEARCH FUNCTION - WITH MISSPELLINGS
        // ============================================
        document.getElementById('search-btn').addEventListener('click', async () => {
            const searchTerm = document.getElementById('search-input').value.trim().toLowerCase();
            if (!searchTerm) {
                document.getElementById('search-result').innerHTML = '❌ Please enter a search term';
                return;
            }

            document.getElementById('search-result').innerHTML = '🔍 Searching...';

            if (!currentUserLat || !currentUserLng) {
                try {
                    await getUserLocation();
                } catch (error) {
                    document.getElementById('search-result').innerHTML = '❌ ' + error;
                    return;
                }
            }

            // First, check if it's a hospital name
            let matchedHospital = null;
            for (let [key, hospitalName] of Object.entries(searchDictionary)) {
                if (key === 'icu' || key === 'trauma' || key === 'maternity' || 
                    key === 'malaria' || key === 'fever' || key === 'pharmacy' || 
                    key === 'xray' || key === 'lab' || key === 'blood' || 
                    key === 'vaccination' || key === 'covid' || key === 'hiv' || 
                    key === 'tb' || key === 'dental' || key === 'eye' || 
                    key === 'physio' || key === 'cancer' || key === 'screening' || 
                    key === 'ultrasound' || key === 'mri' || key === 'ct' || 
                    key === 'surgery') {
                    continue; // Skip service keywords
                }
                
                if (searchTerm.includes(key) || key.includes(searchTerm)) {
                    matchedHospital = hospitalName;
                    break;
                }
            }

            let matchingHospitals = [];

            if (matchedHospital) {
                // Search for specific hospital
                matchingHospitals = allHospitals.filter(h => 
                    h.name.toLowerCase().includes(matchedHospital.toLowerCase())
                );
            } else {
                // Find matching service from dictionary
                let matchedService = null;
                let matchedKeyword = null;
                
                for (let [key, value] of Object.entries(searchDictionary)) {
                    if (searchTerm.includes(key) || key.includes(searchTerm) || 
                        this.similarity(searchTerm, key) > 0.7) {
                        matchedService = value;
                        matchedKeyword = key;
                        break;
                    }
                }

                if (matchedService) {
                    // Check core services
                    if (matchedService === 'icu' || matchedService === 'trauma' || matchedService === 'maternity') {
                        matchingHospitals = allHospitals.filter(h => h[`has_${matchedService}`]);
                    } else {
                        // Search in other_services
                        matchingHospitals = allHospitals.filter(h => 
                            h.other_services && h.other_services.toLowerCase().includes(matchedService)
                        );
                        
                        // If no matches, try fuzzy search in other_services
                        if (matchingHospitals.length === 0) {
                            matchingHospitals = allHospitals.filter(h => {
                                if (!h.other_services) return false;
                                const services = h.other_services.toLowerCase();
                                return services.includes(matchedService) || 
                                       services.includes(matchedKeyword) ||
                                       this.similarity(searchTerm, services) > 0.6;
                            });
                        }
                    }
                }
            }

            // If still no matches, try searching in hospital names
            if (matchingHospitals.length === 0) {
                matchingHospitals = allHospitals.filter(h => 
                    h.name.toLowerCase().includes(searchTerm) ||
                    (h.other_services && h.other_services.toLowerCase().includes(searchTerm))
                );
            }

            if (matchingHospitals.length === 0) {
                document.getElementById('search-result').innerHTML = '❌ No hospitals found matching "' + searchTerm + '"';
                return;
            }

            // Calculate distances and find nearest
            const hospitalsWithDistance = matchingHospitals.map(h => ({
                ...h,
                distance: calculateDistance(
                    currentUserLat, currentUserLng,
                    parseFloat(h.latitude), parseFloat(h.longitude)
                )
            })).sort((a, b) => a.distance - b.distance);

            const nearest = hospitalsWithDistance[0];
            const minutes = Math.round((nearest.distance / 30) * 60);

            document.getElementById('search-result').innerHTML = 
                `✅ Nearest: ${nearest.name} (${nearest.distance.toFixed(1)} km, ~${minutes} mins)`;

            // Route to hospital
            if (routingControl) map.removeControl(routingControl);
            
            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(currentUserLat, currentUserLng),
                    L.latLng(parseFloat(nearest.latitude), parseFloat(nearest.longitude))
                ],
                show: false,
                lineOptions: { styles: [{ color: '#3b82f6', weight: 5 }] },
                createMarker: () => null
            }).addTo(map);

            routingControl.on('routesfound', function(e) {
                currentRoute = e.routes[0];
                currentHospital = nearest;
                clearRouteBtn.classList.remove('hidden');
                
                if (audioEnabled) {
                    speakDirections(currentRoute, nearest.name);
                }
            });

            // Open popup for the nearest hospital
            markers.forEach(m => {
                if (Math.abs(m.getLatLng().lat - parseFloat(nearest.latitude)) < 0.0001 &&
                    Math.abs(m.getLatLng().lng - parseFloat(nearest.longitude)) < 0.0001) {
                    m.openPopup();
                }
            });
        });

        // Simple string similarity for fuzzy matching
        function similarity(s1, s2) {
            let longer = s1;
            let shorter = s2;
            if (s1.length < s2.length) {
                longer = s2;
                shorter = s1;
            }
            const longerLength = longer.length;
            if (longerLength === 0) {
                return 1.0;
            }
            return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
        }

        function editDistance(s1, s2) {
            s1 = s1.toLowerCase();
            s2 = s2.toLowerCase();
            const costs = [];
            for (let i = 0; i <= s1.length; i++) {
                let lastValue = i;
                for (let j = 0; j <= s2.length; j++) {
                    if (i === 0)
                        costs[j] = j;
                    else if (j > 0) {
                        let newValue = costs[j - 1];
                        if (s1.charAt(i - 1) !== s2.charAt(j - 1))
                            newValue = Math.min(Math.min(newValue, lastValue), costs[j]) + 1;
                        costs[j - 1] = lastValue;
                        lastValue = newValue;
                    }
                }
                if (i > 0)
                    costs[s2.length] = lastValue;
            }
            return costs[s2.length];
        }

        // ============================================
        // SPEAK DIRECTIONS
        // ============================================
        function speakDirections(route, hospitalName) {
            if (!route || !route.instructions || !audioEnabled) return;
            
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
                speechTimers.forEach(timer => clearTimeout(timer));
                speechTimers = [];
            }
            
            const synth = window.speechSynthesis;
            const instructions = route.instructions;
            
            const intro = new SpeechSynthesisUtterance(`Directions to ${hospitalName}`);
            intro.rate = 0.9;
            synth.speak(intro);
            
            if (instructions.length > 0) {
                instructions.forEach((instruction, index) => {
                    const timer = setTimeout(() => {
                        if (!audioEnabled) return;
                        
                        const cleanText = instruction.text.replace(/<[^>]*>/g, '');
                        const distKm = (instruction.distance / 1000).toFixed(1);
                        
                        const msg = new SpeechSynthesisUtterance(`In ${distKm} kilometers, ${cleanText}`);
                        msg.rate = 0.9;
                        synth.speak(msg);
                    }, (index + 1) * 8000);
                    
                    speechTimers.push(timer);
                });
            }
        }

        // Clear route
        window.clearRoute = function() {
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            clearRouteBtn.classList.add('hidden');
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
                speechTimers.forEach(timer => clearTimeout(timer));
                speechTimers = [];
            }
            currentRoute = null;
            currentHospital = null;
        };

        // Auto refresh
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) loadHospitals();
        });
        
        window.addEventListener('focus', loadHospitals);

        // Initial load
        loadHospitals();
        getUserLocation().catch(() => {});
    </script>
</body>
</html>