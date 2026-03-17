<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;

class AmbulanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $facility = auth()->user()->facility;
        
        $query = AmbulanceRequest::where('facility_id', $facility->id);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $requests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $statuses = AmbulanceRequest::getStatuses();
        
        return view('admin.ambulance-requests', compact('requests', 'statuses'));
    }
    
    public function update(Request $request, $id)
    {
        $ambulanceRequest = AmbulanceRequest::findOrFail($id);
        
        // Ensure this request belongs to the admin's facility
        if ($ambulanceRequest->facility_id != auth()->user()->facility_id) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:pending,dispatched,en_route,arrived,completed,cancelled',
        ]);
        
        $oldStatus = $ambulanceRequest->status;
        $ambulanceRequest->status = $request->status;
        
        // Set timestamps based on status
        if ($request->status == 'dispatched' && !$ambulanceRequest->dispatched_at) {
            $ambulanceRequest->dispatched_at = now();
        } elseif ($request->status == 'arrived' && !$ambulanceRequest->arrived_at) {
            $ambulanceRequest->arrived_at = now();
        } elseif ($request->status == 'completed' && !$ambulanceRequest->completed_at) {
            $ambulanceRequest->completed_at = now();
        }
        
        $ambulanceRequest->save();
        
        return redirect()->back()->with('success', "Request status updated from {$oldStatus} to {$request->status}");
    }
}