<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cached Hospitals - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .offline-badge {
            background: #f59e0b;
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">📱 BEL-CARE Offline Mode</h1>
            <p class="text-sm opacity-90">Cached hospital data – available without internet</p>
        </div>
    </div>
    
    <div class="container mx-auto p-4">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-yellow-600 text-xl">📡</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        You're viewing cached data. Last synced: {{ now()->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($facilities as $hospital)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-start">
                    <h3 class="font-semibold text-lg text-blue-600">{{ $hospital->name }}</h3>
                    <span class="offline-badge">CACHED</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ $hospital->address }}</p>
                <p class="text-sm mt-2">📞 {{ $hospital->phone }}</p>
                
                <div class="flex flex-wrap gap-1 mt-2">
                    @if($hospital->has_icu)
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">🏥 ICU</span>
                    @endif
                    @if($hospital->has_trauma)
                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">🚨 Trauma</span>
                    @endif
                    @if($hospital->has_maternity)
                        <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">👶 Maternity</span>
                    @endif
                </div>
                
                <div class="mt-4 pt-3 border-t">
                    <p class="text-xs text-gray-500">📍 Coordinates: {{ $hospital->latitude }}, {{ $hospital->longitude }}</p>
                    <p class="text-xs text-gray-500 mt-1">Last updated: {{ $hospital->updated_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-6 text-center">
            <a href="/" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                ← Back to Online Mode
            </a>
        </div>
    </div>
    
    <script>
        // Check online status
        function updateOnlineStatus() {
            if (navigator.onLine) {
                console.log('You are online');
            } else {
                console.log('You are offline - using cached data');
            }
        }
        
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();
    </script>
</body>
</html>