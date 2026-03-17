<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalResource extends Model
{
    protected $fillable = [
        'facility_id',
        'icu_beds_total', 'icu_beds_available',
        'general_beds_total', 'general_beds_available',
        'maternity_beds_total', 'maternity_beds_available',
        'pediatric_beds_total', 'pediatric_beds_available',
        'malaria_meds', 'pain_relief', 'antibiotics', 'iv_fluids',
        'blood_a_positive', 'blood_a_negative',
        'blood_b_positive', 'blood_b_negative',
        'blood_o_positive', 'blood_o_negative',
        'blood_ab_positive', 'blood_ab_negative',
        'doctors_on_duty', 'nurses_on_duty',
        'ventilators_available',
        'xray_available', 'ct_scan_available', 'mri_available',
        'ambulances_available',
        'last_updated_at', 'updated_by'
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
        'malaria_meds' => 'boolean',
        'pain_relief' => 'boolean',
        'antibiotics' => 'boolean',
        'iv_fluids' => 'boolean',
        'xray_available' => 'boolean',
        'ct_scan_available' => 'boolean',
        'mri_available' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
