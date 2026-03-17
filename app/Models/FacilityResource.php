<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityResource extends Model
{
    protected $table = 'facility_resources';
    
    protected $fillable = [
        'facility_id',
        'icu_beds_total',
        'icu_beds_available',
        'general_beds_total',
        'general_beds_available',
        'maternity_beds_total',
        'maternity_beds_available',
        'doctors_on_duty',
        'nurses_on_duty',
        'paramedics_on_duty',
        'ventilators_available',
        'ventilator_count',
        'ambulances_available',
        'ambulance_count',
        'last_updated'
    ];

    protected $casts = [
        'ventilators_available' => 'boolean',
        'ambulances_available' => 'boolean',
        'last_updated' => 'datetime',
        'icu_beds_total' => 'integer',
        'icu_beds_available' => 'integer',
        'general_beds_total' => 'integer',
        'general_beds_available' => 'integer',
        'maternity_beds_total' => 'integer',
        'maternity_beds_available' => 'integer',
        'doctors_on_duty' => 'integer',
        'nurses_on_duty' => 'integer',
        'paramedics_on_duty' => 'integer',
        'ventilator_count' => 'integer',
        'ambulance_count' => 'integer'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}