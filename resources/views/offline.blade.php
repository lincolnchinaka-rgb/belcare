<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Offline - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md text-center">
            <div class="text-6xl mb-4">📡</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">You're Offline</h1>
            <p class="text-gray-600 mb-6">
                Don't worry! You can still access previously viewed hospitals.
            </p>
            
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <h2 class="font-semibold text-blue-800 mb-2">Offline Mode Active</h2>
                <p class="text-sm text-blue-600">
                    When you're back online, your data will sync automatically.
                </p>
            </div>
            
            <div class="space-y-3">
                <button onclick="window.location.href='/'" 
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Try Again
                </button>
                
                <button onclick="window.location.href='/cached-hospitals'" 
                        class="w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition">
                    View Cached Hospitals
                </button>
            </div>
            
            <p class="text-xs text-gray-400 mt-6">
                BEL-CARE • Emergency healthcare locator for Bulawayo
            </p>
        </div>
    </div>
</body>
</html>