<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HospitalAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'hospital_admin') {
            return $next($request);
        }
        
        abort(403, 'Unauthorized. Hospital admin access required.');
    }
}