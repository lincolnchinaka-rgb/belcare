<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbulanceRequest extends Model
{
    protected $fillable = [
        'facility_id',
        'patient_name',
        'patient_phone',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'notes',
        'status',
        'dispatched_at',
        'en_route_at',
        'arrived_at',
        'completed_at',
        'dispatched_by'
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'en_route_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }
}