<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\FacilityResource;

class FacilityResourceSeeder extends Seeder
{
    public function run(): void
    {
        // Get all facilities
        $facilities = Facility::all();

        if ($facilities->isEmpty()) {
            $this->command->info('No facilities found. Please run FacilitySeeder first.');
            return;
        }

        foreach ($facilities as $facility) {
            // Check if resource already exists
            $existing = FacilityResource::where('facility_id', $facility->id)->first();
            
            if (!$existing) {
                FacilityResource::create([
                    'facility_id' => $facility->id,
                    'icu_beds_total' => rand(5, 20),
                    'icu_beds_available' => rand(0, 8),
                    'general_beds_total' => rand(50, 200),
                    'general_beds_available' => rand(5, 50),
                    'maternity_beds_total' => $facility->has_maternity ? rand(10, 40) : 0,
                    'maternity_beds_available' => $facility->has_maternity ? rand(2, 15) : 0,
                    'doctors_on_duty' => rand(3, 15),
                    'nurses_on_duty' => rand(10, 50),
                    'paramedics_on_duty' => rand(2, 8),
                    'ventilators_available' => (rand(0, 1) == 1),
                    'ventilator_count' => rand(0, 10),
                    'ambulances_available' => (rand(0, 1) == 1),
                    'ambulance_count' => rand(0, 5),
                    'last_updated' => now(),
                ]);
            }
        }

        $this->command->info('Facility resources seeded successfully!');
    }
}