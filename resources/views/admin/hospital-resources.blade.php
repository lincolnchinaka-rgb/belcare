<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Hospital Resources - BEL-CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">🚑 Update Hospital Resources</h1>
                <p class="text-sm opacity-90">{{ $user->name }} - {{ $facility->name }}</p>
            </div>
            <a href="/admin" class="bg-gray-500 px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Back to Dashboard</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Update Resources for {{ $facility->name }}</h2>
            
            <form method="POST" action="{{ route('admin.resources.update') }}">
                @csrf
                @method('PUT')
                
                <!-- Bed Availability Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">🛏️ Bed Availability</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">ICU Beds (Total)</label>
                            <input type="number" name="icu_beds_total" value="{{ old('icu_beds_total', $resources->icu_beds_total ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">ICU Beds (Available)</label>
                            <input type="number" name="icu_beds_available" value="{{ old('icu_beds_available', $resources->icu_beds_available ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">General Beds (Total)</label>
                            <input type="number" name="general_beds_total" value="{{ old('general_beds_total', $resources->general_beds_total ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">General Beds (Available)</label>
                            <input type="number" name="general_beds_available" value="{{ old('general_beds_available', $resources->general_beds_available ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Maternity Beds (Total)</label>
                            <input type="number" name="maternity_beds_total" value="{{ old('maternity_beds_total', $resources->maternity_beds_total ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Maternity Beds (Available)</label>
                            <input type="number" name="maternity_beds_available" value="{{ old('maternity_beds_available', $resources->maternity_beds_available ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pediatric Beds (Total)</label>
                            <input type="number" name="pediatric_beds_total" value="{{ old('pediatric_beds_total', $resources->pediatric_beds_total ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pediatric Beds (Available)</label>
                            <input type="number" name="pediatric_beds_available" value="{{ old('pediatric_beds_available', $resources->pediatric_beds_available ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                    </div>
                </div>
                
                <!-- Staff Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">👨‍⚕️ Staff on Duty</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Doctors</label>
                            <input type="number" name="doctors_on_duty" value="{{ old('doctors_on_duty', $resources->doctors_on_duty ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nurses</label>
                            <input type="number" name="nurses_on_duty" value="{{ old('nurses_on_duty', $resources->nurses_on_duty ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Paramedics</label>
                            <input type="number" name="paramedics_on_duty" value="{{ old('paramedics_on_duty', $resources->paramedics_on_duty ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                    </div>
                </div>
                
                <!-- Equipment Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">⚡ Equipment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Number of Ventilators</label>
                            <input type="number" name="ventilator_count" value="{{ old('ventilator_count', $resources->ventilator_count ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="ventilators_available" value="1" {{ old('ventilators_available', $resources->ventilators_available ?? false) ? 'checked' : '' }} class="mr-2 h-5 w-5">
                                <span class="text-gray-700">Ventilators Available</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Ambulances Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">🚑 Ambulances</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Number of Ambulances</label>
                            <input type="number" name="ambulance_count" value="{{ old('ambulance_count', $resources->ambulance_count ?? 0) }}" class="w-full px-3 py-2 border rounded-lg" min="0">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="ambulances_available" value="1" {{ old('ambulances_available', $resources->ambulances_available ?? false) ? 'checked' : '' }} class="mr-2 h-5 w-5">
                                <span class="text-gray-700">Ambulances Available</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 border-t pt-6">
                    <a href="/admin" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Resources
                    </button>
                </div>
                
                @if($resources->last_updated_at)
                <div class="mt-4 text-sm text-gray-500 text-right">
                    Last updated: {{ $resources->last_updated_at->diffForHumans() }}
                </div>
                @endif
            </form>
        </div>
    </div>
</body>
</html>