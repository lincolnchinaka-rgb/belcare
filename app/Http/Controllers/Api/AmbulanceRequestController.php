<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AmbulanceRequestController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'facility_id' => 'required|exists:facilities,id',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'patient_name' => 'nullable|string|max:255',
                'patient_phone' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
            ]);

            $ambulanceRequest = AmbulanceRequest::create([
                'facility_id' => $validated['facility_id'],
                'pickup_latitude' => $validated['latitude'],
                'pickup_longitude' => $validated['longitude'],
                'patient_name' => $validated['patient_name'] ?? null,
                'patient_phone' => $validated['patient_phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'pickup_address' => 'Location from map',
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ambulance request created successfully',
                'request' => [
                    'id' => $ambulanceRequest->id,
                    'status' => $ambulanceRequest->status
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ambulance request'
            ], 500);
        }
    }

    public function location($id)
    {
        try {
            // Cache for 30 seconds to reduce database load
            $request = Cache::remember('ambulance_location_' . $id, 30, function() use ($id) {
                return AmbulanceRequest::with('facility')->find($id);
            });
            
            if (!$request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'pickup_latitude' => $request->pickup_latitude,
                    'pickup_longitude' => $request->pickup_longitude,
                    'status' => $request->status,
                    'facility_id' => $request->facility_id,
                    'patient_name' => $request->patient_name,
                    'patient_phone' => $request->patient_phone,
                    'facility' => $request->facility ? [
                        'name' => $request->facility->name,
                        'phone' => $request->facility->phone
                    ] : null,
                    'timestamps' => [
                        'created_at' => $request->created_at ? $request->created_at->format('M d, Y H:i') : null,
                        'dispatched_at' => $request->dispatched_at ? $request->dispatched_at->format('M d, Y H:i') : null,
                        'en_route_at' => $request->en_route_at ? $request->en_route_at->format('M d, Y H:i') : null,
                        'arrived_at' => $request->arrived_at ? $request->arrived_at->format('M d, Y H:i') : null,
                        'completed_at' => $request->completed_at ? $request->completed_at->format('M d, Y H:i') : null
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }
    }
}