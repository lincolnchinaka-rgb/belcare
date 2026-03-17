<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class CachedHospitalController extends Controller
{
    public function index()
    {
        // Get all hospitals for caching
        $facilities = Facility::all();
        
        return view('cached-hospitals', compact('facilities'));
    }
    
    public function offline()
    {
        return view('offline');
    }
}