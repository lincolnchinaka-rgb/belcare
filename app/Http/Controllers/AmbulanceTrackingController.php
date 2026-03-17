<?php

namespace App\Http\Controllers;

use App\Models\AmbulanceRequest;
use Illuminate\Http\Request;

class AmbulanceTrackingController extends Controller
{
    public function show($id)
    {
        $request = AmbulanceRequest::with('facility')->findOrFail($id);
        return view('ambulance-tracking', compact('request'));
    }
}