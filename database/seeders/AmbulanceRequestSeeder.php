<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AmbulanceRequest;
use App\Models\Facility;
use App\Models\User;
use Faker\Factory as Faker;

class AmbulanceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get all facilities
        $facilities = Facility::all();
        
        if ($facilities->isEmpty()) {
            $this->command->error('❌ No facilities found!');
            return;
        }

        $statuses = ['pending', 'dispatched', 'en_route', 'arrived', 'completed', 'cancelled'];
        $now = now();
        
        $this->command->info('Creating test ambulance requests...');
        
        // Delete existing requests
        AmbulanceRequest::truncate();
        
        // Create 5 requests for each facility
        foreach ($facilities as $facility) {
            for ($i = 1; $i <= 5; $i++) {
                $status = $statuses[array_rand($statuses)];
                $createdAt = $now->copy()->subHours(rand(1, 48));
                
                $requestData = [
                    'facility_id' => $facility->id,
                    'patient_name' => $faker->name(),
                    'patient_phone' => '07' . rand(7, 9) . rand(1000000, 9999999),
                    'pickup_address' => $faker->address(),
                    'pickup_latitude' => (float)$facility->latitude + (rand(-100, 100) / 1000),
                    'pickup_longitude' => (float)$facility->longitude + (rand(-100, 100) / 1000),
                    'notes' => rand(0, 1) ? $faker->sentence() : null,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
                
                $request = AmbulanceRequest::create($requestData);

                // Add timestamps based on status
                if ($status != 'pending') {
                    $request->dispatched_at = $createdAt->copy()->addMinutes(rand(1, 5));
                }
                if (in_array($status, ['en_route', 'arrived', 'completed'])) {
                    $request->en_route_at = $createdAt->copy()->addMinutes(rand(6, 10));
                }
                if (in_array($status, ['arrived', 'completed'])) {
                    $request->arrived_at = $createdAt->copy()->addMinutes(rand(11, 15));
                }
                if ($status == 'completed') {
                    $request->completed_at = $createdAt->copy()->addMinutes(rand(16, 30));
                }
                if (in_array($status, ['dispatched', 'en_route', 'arrived', 'completed'])) {
                    $dispatchers = User::where('facility_id', $facility->id)->get();
                    if ($dispatchers->isNotEmpty()) {
                        $request->dispatched_by = $dispatchers->random()->id;
                    }
                }
                
                $request->save();
            }
            $this->command->info("✅ Created 5 requests for {$facility->name}");
        }
        
        $this->command->info('====================================');
        $this->command->info('✅ Total requests created: ' . AmbulanceRequest::count());
    }
}