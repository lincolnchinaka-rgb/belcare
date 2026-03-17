<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\HospitalResource;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', function () {
    return view('home');
});

// ============================================
// FIXED LOGIN ENDPOINT - WITH SESSION REGENERATION
// ============================================
Route::post('/simple-login', function (Request $request) {
    try {
        $email = $request->input('email');
        $password = $request->input('password');
        
        if (!$email || !$password) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password required'
            ], 400);
        }
        
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            
            // REGENERATE SESSION TO PREVENT FIXATION
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'facility_id' => $user->facility_id
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Admin dashboard
Route::get('/admin', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return view('admin.dashboard', ['user' => $user]);
    }
    return redirect('/');
})->name('admin.dashboard');

// Admin resources page - VIEW
Route::get('/admin/resources', function () {
    if (Auth::check()) {
        $user = Auth::user();
        
        $facility = Facility::where('id', $user->facility_id)->first();
        
        if (!$facility) {
            return "No facility found for this user. Please contact administrator.";
        }
        
        $resources = HospitalResource::where('facility_id', $facility->id)->first();
        
        if (!$resources) {
            $resources = new HospitalResource();
            $resources->facility_id = $facility->id;
            $resources->icu_beds_total = 0;
            $resources->icu_beds_available = 0;
            $resources->general_beds_total = 0;
            $resources->general_beds_available = 0;
            $resources->maternity_beds_total = 0;
            $resources->maternity_beds_available = 0;
            $resources->pediatric_beds_total = 0;
            $resources->pediatric_beds_available = 0;
            $resources->doctors_on_duty = 0;
            $resources->nurses_on_duty = 0;
            $resources->paramedics_on_duty = 0;
            $resources->ventilators_available = false;
            $resources->ventilator_count = 0;
            $resources->ambulances_available = false;
            $resources->ambulance_count = 0;
            $resources->save();
        }
        
        return view('admin.hospital-resources', [
            'facility' => $facility,
            'resources' => $resources,
            'user' => $user
        ]);
    }
    return redirect('/');
})->name('admin.resources');

// Admin resources page - UPDATE
Route::put('/admin/resources', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();
        $facility = Facility::where('id', $user->facility_id)->first();
        
        if (!$facility) {
            return redirect()->back()->with('error', 'Facility not found');
        }
        
        $resources = HospitalResource::where('facility_id', $facility->id)->first();
        
        if (!$resources) {
            $resources = new HospitalResource();
            $resources->facility_id = $facility->id;
        }
        
        $resources->icu_beds_total = $request->icu_beds_total ?? 0;
        $resources->icu_beds_available = $request->icu_beds_available ?? 0;
        $resources->general_beds_total = $request->general_beds_total ?? 0;
        $resources->general_beds_available = $request->general_beds_available ?? 0;
        $resources->maternity_beds_total = $request->maternity_beds_total ?? 0;
        $resources->maternity_beds_available = $request->maternity_beds_available ?? 0;
        $resources->pediatric_beds_total = $request->pediatric_beds_total ?? 0;
        $resources->pediatric_beds_available = $request->pediatric_beds_available ?? 0;
        $resources->doctors_on_duty = $request->doctors_on_duty ?? 0;
        $resources->nurses_on_duty = $request->nurses_on_duty ?? 0;
        $resources->paramedics_on_duty = $request->paramedics_on_duty ?? 0;
        $resources->ventilators_available = $request->has('ventilators_available');
        $resources->ventilator_count = $request->ventilator_count ?? 0;
        $resources->ambulances_available = $request->has('ambulances_available');
        $resources->ambulance_count = $request->ambulance_count ?? 0;
        $resources->last_updated_at = now();
        
        $resources->save();
        
        return redirect('/admin')->with('success', 'Resources updated successfully!');
    }
    return redirect('/');
})->name('admin.resources.update');

// ============================================
// ADMIN AMBULANCE REQUESTS - WITH HOSPITAL ISOLATION
// ============================================
Route::get('/admin/ambulance-requests', function () {
    if (Auth::check()) {
        $user = Auth::user();
        
        $facility = Facility::where('id', $user->facility_id)->first();
        
        if (!$facility) {
            return redirect('/admin')->with('error', 'Facility not found');
        }
        
        $requests = App\Models\AmbulanceRequest::where('facility_id', $user->facility_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('admin.ambulance-requests', [
            'user' => $user,
            'facility' => $facility,
            'requests' => $requests
        ]);
    }
    return redirect('/');
})->name('admin.ambulance');

// Admin facility edit - VIEW
Route::get('/admin/facility/edit', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $facility = Facility::where('id', $user->facility_id)->first();
        
        if (!$facility) {
            return redirect('/admin')->with('error', 'Facility not found');
        }
        
        return view('admin.facility-edit', [
            'facility' => $facility,
            'user' => $user
        ]);
    }
    return redirect('/');
})->name('admin.facility.edit');

// Admin facility edit - UPDATE
Route::put('/admin/facility/update', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();
        $facility = Facility::where('id', $user->facility_id)->first();
        
        if (!$facility) {
            return redirect()->back()->with('error', 'Facility not found');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'has_icu' => 'nullable|boolean',
            'has_trauma' => 'nullable|boolean',
            'has_maternity' => 'nullable|boolean',
            'other_services' => 'nullable|string',
        ]);
        
        $facility->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'has_icu' => $request->has('has_icu'),
            'has_trauma' => $request->has('has_trauma'),
            'has_maternity' => $request->has('has_maternity'),
            'other_services' => $validated['other_services'],
            'last_updated_at' => now(),
            'updated_by' => $user->id,
        ]);
        
        return redirect('/admin')->with('success', 'Hospital information updated successfully!');
    }
    return redirect('/');
})->name('admin.facility.update');

// ============================================
// ADMIN PROFILE SETTINGS
// ============================================
Route::get('/admin/profile', function () {
    if (Auth::check()) {
        return view('admin.profile');
    }
    return redirect('/');
})->name('admin.profile');

Route::put('/admin/profile/email', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();
        
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required'
        ]);
        
        if (!Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }
        
        $user->update(['email' => $request->email]);
        
        return redirect()->back()->with('success', 'Email updated successfully!');
    }
    return redirect('/');
})->name('admin.profile.email');

Route::put('/admin/profile/password', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);
        
        if (!Auth::attempt(['email' => $user->email, 'password' => $request->current_password])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }
        
        $user->update(['password' => bcrypt($request->new_password)]);
        
        return redirect()->back()->with('success', 'Password changed successfully!');
    }
    return redirect('/');
})->name('admin.profile.password');

// ============================================
// LOGOUT ROUTE - FIXED WITH REQUEST PARAMETER
// ============================================
Route::get('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Track ambulance page
Route::get('/track/{id}', function ($id) {
    return view('track');
});

// Debug route - Check authentication (HTML)
Route::get('/check-auth', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $facility = Facility::find($user->facility_id);
        $facilityName = $facility ? $facility->name : 'No facility';
        
        return "✅ Logged in as: " . $user->email . "<br>
                👤 Name: " . $user->name . "<br>
                🏥 Facility: " . $facilityName . " (ID: " . $user->facility_id . ")<br><br> 
                <a href='/admin' style='background:blue; color:white; padding:10px; text-decoration:none; border-radius:5px;'>Go to Admin Dashboard</a>";
    } else {
        return "❌ Not logged in <br><br> 
                <a href='/' style='background:green; color:white; padding:10px; text-decoration:none; border-radius:5px;'>Go to Homepage to Login</a>";
    }
});

// Test ambulance endpoint
Route::get('/test-ambulance-requests', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $requests = App\Models\AmbulanceRequest::where('facility_id', $user->facility_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return response()->json([
            'success' => true,
            'user' => $user->email,
            'facility_id' => $user->facility_id,
            'count' => $requests->count(),
            'data' => $requests
        ]);
    } else {
        return response()->json(['success' => false, 'message' => 'Not logged in'], 401);
    }
});

// ============================================
// DEBUG - Check authentication (JSON version)
// ============================================
Route::get('/debug-auth', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $facility = App\Models\Facility::find($user->facility_id);
        
        return response()->json([
            'authenticated' => true,
            'user' => $user->email,
            'facility' => $facility ? $facility->name : 'Unknown',
            'facility_id' => $user->facility_id
        ]);
    } else {
        return response()->json([
            'authenticated' => false,
            'message' => 'Not logged in'
        ]);
    }
});