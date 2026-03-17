<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\AmbulanceRequestController;

// Public routes
Route::get('/facilities', [FacilityController::class, 'index']);
Route::get('/facilities/nearest', [FacilityController::class, 'nearest']);
Route::post('/ambulance/request', [AmbulanceRequestController::class, 'store']);
Route::get('/ambulance/location/{id}', [AmbulanceRequestController::class, 'location']);

// ============================================
// ADMIN ROUTES - Using web middleware for session
// ============================================
Route::middleware(['web'])->group(function () {
    
    // Get ambulance requests for the logged-in admin
    Route::get('/ambulance/requests', function (Request $request) {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }

            $user = Auth::user();
            
            $requests = App\Models\AmbulanceRequest::where('facility_id', $user->facility_id)
                        ->orderBy('created_at', 'desc')
                        ->limit(50)
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $requests
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading requests'
            ], 500);
        }
    });

    // Update ambulance request status
    Route::put('/ambulance/requests/{id}/status', function (Request $request, $id) {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }

            $user = Auth::user();
            
            $ambulanceRequest = App\Models\AmbulanceRequest::where('id', $id)
                                ->where('facility_id', $user->facility_id)
                                ->first();

            if (!$ambulanceRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found'
                ], 404);
            }

            $status = $request->input('status');
            $validStatuses = ['pending', 'dispatched', 'en_route', 'arrived', 'completed', 'cancelled'];

            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status'
                ], 400);
            }

            $updateData = ['status' => $status];

            switch($status) {
                case 'dispatched':
                    $updateData['dispatched_at'] = now();
                    $updateData['dispatched_by'] = $user->id;
                    break;
                case 'en_route':
                    $updateData['en_route_at'] = now();
                    break;
                case 'arrived':
                    $updateData['arrived_at'] = now();
                    break;
                case 'completed':
                    $updateData['completed_at'] = now();
                    break;
            }

            $ambulanceRequest->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status'
            ], 500);
        }
    });
});