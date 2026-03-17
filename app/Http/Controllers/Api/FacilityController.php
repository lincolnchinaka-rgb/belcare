<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\HospitalResource;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        try {
            $facilities = Facility::all();
            
            // Manually load fresh resources for each facility
            foreach ($facilities as $facility) {
                $facility->resources = HospitalResource::where('facility_id', $facility->id)->first();
            }
            
            return response()->json([
                'success' => true,
                'count' => $facilities->count(),
                'data' => $facilities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load facilities',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function nearest(Request $request)
    {
        try {
            $facilities = Facility::all();
            
            // Manually load fresh resources for each facility
            foreach ($facilities as $facility) {
                $facility->resources = HospitalResource::where('facility_id', $facility->id)->first();
            }
            
            if ($request->has('services')) {
                $services = $request->services;
                $facilities = $facilities->filter(function($facility) use ($services) {
                    foreach ($services as $service) {
                        if (!$facility->{"has_$service"}) {
                            return false;
                        }
                    }
                    return true;
                });
            }
            
            return response()->json([
                'success' => true,
                'count' => $facilities->count(),
                'data' => $facilities->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load facilities',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}