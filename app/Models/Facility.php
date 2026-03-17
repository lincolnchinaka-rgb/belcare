<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = [
        'name', 
        'phone', 
        'address', 
        'latitude', 
        'longitude',
        'has_icu', 
        'has_trauma', 
        'has_maternity', 
        'other_services',
        'last_updated_at', 
        'updated_by'
    ];

    protected $casts = [
        'has_icu' => 'boolean',
        'has_trauma' => 'boolean',
        'has_maternity' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    public function resources()
    {
        return $this->hasOne(FacilityResource::class, 'facility_id');
    }
}