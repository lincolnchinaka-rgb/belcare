<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">👤 Profile Settings</h1>
                <p class="text-sm opacity-90">{{ auth()->user()->name }} - {{ auth()->user()->email }}</p>
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

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Update Email Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">📧 Update Email</h2>
                
                <form method="POST" action="{{ route('admin.profile.email') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" disabled 
                               class="w-full px-3 py-2 border rounded-lg bg-gray-100">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">New Email</label>
                        <input type="email" name="email" required 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                        <input type="password" name="password" required 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Enter your password to confirm changes</p>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Email
                    </button>
                </form>
            </div>

            <!-- Update Password Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">🔒 Change Password</h2>
                
                <form method="POST" action="{{ route('admin.profile.password') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Password</label>
                        <input type="password" name="current_password" required 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                        <input type="password" name="new_password" required 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" required 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                        Change Password
                    </button>
                </form>
            </div>

            <!-- Account Info -->
            <div class="md:col-span-2 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">📋 Account Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500 text-sm">Name</p>
                        <p class="font-semibold">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Email</p>
                        <p class="font-semibold">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Role</p>
                        <p class="font-semibold capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Member Since</p>
                        <p class="font-semibold">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>