<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function edit()
    {
        $facility = auth()->user()->facility;
        return view('admin.facility-edit', compact('facility'));
    }

    public function update(Request $request)
    {
        $facility = auth()->user()->facility;
        
        $request->validate([
            'has_icu' => 'sometimes|boolean',
            'has_trauma' => 'sometimes|boolean',
            'has_maternity' => 'sometimes|boolean',
            'other_services' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $facility->update([
            'has_icu' => $request->has_icu ?? false,
            'has_trauma' => $request->has_trauma ?? false,
            'has_maternity' => $request->has_maternity ?? false,
            'other_services' => $request->other_services,
            'phone' => $request->phone,
            'address' => $request->address,
            'last_updated_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Hospital services updated successfully!');
    }
}