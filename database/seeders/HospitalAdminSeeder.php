<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Support\Facades\Hash;

class HospitalAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get all facilities
        $facilities = Facility::all();
        
        if ($facilities->isEmpty()) {
            $this->command->error('❌ No facilities found! Please run HospitalSeeder first.');
            return;
        }
        
        $this->command->info('Found ' . $facilities->count() . ' facilities');
        
        // Define admin credentials for each facility
        $admins = [
            ['name' => 'Mpilo Admin', 'email' => 'mpilo@belcare.com', 'facility_name' => 'Mpilo Central Hospital'],
            ['name' => 'UBH Admin', 'email' => 'ubh@belcare.com', 'facility_name' => 'United Bulawayo Hospitals (UBH)'],
            ['name' => 'Mater Dei Admin', 'email' => 'materdei@belcare.com', 'facility_name' => 'Mater Dei Hospital'],
            ['name' => 'Nkulumane Admin', 'email' => 'nkulumane@belcare.com', 'facility_name' => 'Nkulumane Clinic'],
            ['name' => 'Thorngrove Admin', 'email' => 'thorngrove@belcare.com', 'facility_name' => 'Thorngrove Infectious Diseases Hospital'],
            ['name' => 'Pelandaba Admin', 'email' => 'pelandaba@belcare.com', 'facility_name' => 'Pelandaba Clinic'],
            ['name' => 'Hillside Admin', 'email' => 'hillside@belcare.com', 'facility_name' => 'Hillside Clinic'],
            ['name' => 'Royal Women Admin', 'email' => 'royalwomen@belcare.com', 'facility_name' => 'Royal Women’s Clinic'],
            ['name' => 'Premier Admin', 'email' => 'premier@belcare.com', 'facility_name' => 'Premier Hillside Hospital'],
            ['name' => 'Emergency Admin', 'email' => 'emergency@belcare.com', 'facility_name' => 'Emergency Medical Clinic'],
            ['name' => 'Ingutsheni Admin', 'email' => 'ingutsheni@belcare.com', 'facility_name' => 'Ingutsheni Central Hospital'],
            ['name' => 'St Luke Admin', 'email' => 'stluke@belcare.com', 'facility_name' => "St. Luke's Hospital"],
            ['name' => 'Central Admin', 'email' => 'central@belcare.com', 'facility_name' => 'Bulawayo Central Hospital'],
            ['name' => 'Ekhuselweni Admin', 'email' => 'ekhuselweni@belcare.com', 'facility_name' => 'Ekhuselweni Clinic'],
            ['name' => 'Nketa8 Admin', 'email' => 'nketa8@belcare.com', 'facility_name' => 'Nketa 8 Clinic'],
            ['name' => 'Emganwini Admin', 'email' => 'emganwini@belcare.com', 'facility_name' => 'Emganwini Clinic'],
            ['name' => 'Mahatshula Admin', 'email' => 'mahatshula@belcare.com', 'facility_name' => 'Mahatshula Clinic'],
        ];

        // Keep the original admin
        $existingAdmin = User::where('email', 'hospital@belcare.com')->first();
        if (!$existingAdmin) {
            $mpilo = Facility::where('name', 'Mpilo Central Hospital')->first();
            if ($mpilo) {
                User::create([
                    'name' => 'Hospital Admin',
                    'email' => 'hospital@belcare.com',
                    'password' => Hash::make('password123'),
                    'facility_id' => $mpilo->id,
                    'role' => 'admin'
                ]);
                $this->command->info('✅ Created master admin: hospital@belcare.com');
            }
        }

        // Create admin for each facility
        $created = 0;
        $skipped = 0;
        
        foreach ($admins as $adminData) {
            // Check if user already exists
            $existingUser = User::where('email', $adminData['email'])->first();
            if ($existingUser) {
                $this->command->warn("⏭️  Skipped {$adminData['email']} - already exists");
                $skipped++;
                continue;
            }
            
            $facility = Facility::where('name', $adminData['facility_name'])->first();
            
            if ($facility) {
                User::create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'password' => Hash::make('password123'),
                    'facility_id' => $facility->id,
                    'role' => 'admin'
                ]);
                $this->command->info("✅ Created: {$adminData['email']} for {$adminData['facility_name']}");
                $created++;
            } else {
                $this->command->error("❌ Facility not found: {$adminData['facility_name']}");
            }
        }
        
        $this->command->info('====================================');
        $this->command->info("✅ Created {$created} new hospital admins");
        $this->command->info("⏭️  Skipped {$skipped} existing admins");
        $this->command->info('====================================');
        $this->command->info('Default password for all: password123');
        $this->command->info('====================================');
        
        // Display all hospital admins
        $this->command->info('HOSPITAL ADMIN CREDENTIALS:');
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $facility = Facility::find($admin->facility_id);
            $facilityName = $facility ? $facility->name : 'Unknown';
            $this->command->line("   📧 {$admin->email}  |  🏥 {$facilityName}");
        }
        $this->command->info('====================================');
    }
}