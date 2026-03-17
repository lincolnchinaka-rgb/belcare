<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $facility = auth()->user()->facility;
        
        if (!$facility) {
            return redirect()->route('dashboard')->with('error', 'No facility associated with your account.');
        }
        
        $ambulanceRequests = AmbulanceRequest::where('facility_id', $facility->id)
                              ->orderBy('created_at', 'desc')
                              ->limit(10)
                              ->get();
        
        return view('admin.dashboard', compact('facility', 'ambulanceRequests'));
    }
}