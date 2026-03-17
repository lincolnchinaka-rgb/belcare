<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HospitalResource;
use Illuminate\Http\Request;

class HospitalResourceController extends Controller
{
    public function edit()
    {
        $facility = auth()->user()->facility;
        
        // Get or create resources
        $resources = $facility->resources;
        
        if (!$resources) {
            $resources = new HospitalResource([
                'facility_id' => $facility->id,
                'icu_beds_total' => 0,
                'icu_beds_available' => 0,
                'general_beds_total' => 0,
                'general_beds_available' => 0,
                'maternity_beds_total' => 0,
                'maternity_beds_available' => 0,
                'pediatric_beds_total' => 0,
                'pediatric_beds_available' => 0,
                'doctors_on_duty' => 0,
                'nurses_on_duty' => 0,
                'ambulances_available' => 0,
            ]);
            $resources->save();
        }
        
        return view('admin.hospital-resources', compact('facility', 'resources'));
    }

    public function update(Request $request)
    {
        $facility = auth()->user()->facility;
        
        $request->validate([
            // Beds
            'icu_beds_total' => 'required|integer|min:0',
            'icu_beds_available' => 'required|integer|min:0|lte:icu_beds_total',
            'general_beds_total' => 'required|integer|min:0',
            'general_beds_available' => 'required|integer|min:0|lte:general_beds_total',
            'maternity_beds_total' => 'required|integer|min:0',
            'maternity_beds_available' => 'required|integer|min:0|lte:maternity_beds_total',
            'pediatric_beds_total' => 'required|integer|min:0',
            'pediatric_beds_available' => 'required|integer|min:0|lte:pediatric_beds_total',
            
            // Staff
            'doctors_on_duty' => 'required|integer|min:0',
            'nurses_on_duty' => 'required|integer|min:0',
            
            // Ambulances
            'ambulances_available' => 'required|integer|min:0',
            
            // Blood bank
            'blood_a_positive' => 'required|integer|min:0',
            'blood_a_negative' => 'required|integer|min:0',
            'blood_b_positive' => 'required|integer|min:0',
            'blood_b_negative' => 'required|integer|min:0',
            'blood_o_positive' => 'required|integer|min:0',
            'blood_o_negative' => 'required|integer|min:0',
            'blood_ab_positive' => 'required|integer|min:0',
            'blood_ab_negative' => 'required|integer|min:0',
            
            // Equipment
            'ventilators_available' => 'required|integer|min:0',
        ]);

        $resources = $facility->resources;
        
        if (!$resources) {
            $resources = new HospitalResource();
            $resources->facility_id = $facility->id;
        }
        
        // Update all fields
        $resources->icu_beds_total = $request->icu_beds_total;
        $resources->icu_beds_available = $request->icu_beds_available;
        $resources->general_beds_total = $request->general_beds_total;
        $resources->general_beds_available = $request->general_beds_available;
        $resources->maternity_beds_total = $request->maternity_beds_total;
        $resources->maternity_beds_available = $request->maternity_beds_available;
        $resources->pediatric_beds_total = $request->pediatric_beds_total;
        $resources->pediatric_beds_available = $request->pediatric_beds_available;
        
        $resources->malaria_meds = $request->has('malaria_meds');
        $resources->pain_relief = $request->has('pain_relief');
        $resources->antibiotics = $request->has('antibiotics');
        $resources->iv_fluids = $request->has('iv_fluids');
        
        $resources->blood_a_positive = $request->blood_a_positive;
        $resources->blood_a_negative = $request->blood_a_negative;
        $resources->blood_b_positive = $request->blood_b_positive;
        $resources->blood_b_negative = $request->blood_b_negative;
        $resources->blood_o_positive = $request->blood_o_positive;
        $resources->blood_o_negative = $request->blood_o_negative;
        $resources->blood_ab_positive = $request->blood_ab_positive;
        $resources->blood_ab_negative = $request->blood_ab_negative;
        
        $resources->doctors_on_duty = $request->doctors_on_duty;
        $resources->nurses_on_duty = $request->nurses_on_duty;
        
        $resources->ventilators_available = $request->ventilators_available;
        $resources->xray_available = $request->has('xray_available');
        $resources->ct_scan_available = $request->has('ct_scan_available');
        $resources->mri_available = $request->has('mri_available');
        
        $resources->ambulances_available = $request->ambulances_available;
        
        $resources->last_updated_at = now();
        $resources->updated_by = auth()->id();
        
        $resources->save();
        
        return redirect()->route('admin.dashboard')->with('success', 'Hospital resources updated successfully!');
    }
}