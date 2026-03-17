<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnhancedFacilitySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing facilities
        DB::table('facilities')->truncate();
        
        // Phone numbers to assign randomly
        $phones = [
            '071 870 1838',
            '078 497 1900', 
            '078 294 1552',
            '078 691 6483'
        ];
        
        // Comprehensive facilities data
        $facilities = [
            [
                'name' => 'Mpilo Central Hospital',
                'address' => 'Mpilo Road, Bulawayo',
                'latitude' => -20.188798,
                'longitude' => 28.57141,
                'has_icu' => true,
                'has_trauma' => true,
                'has_maternity' => true,
                'other_services' => 'NICU (Neonatal ICU), HIV/AIDS services, Hepatitis B screening, Immunizations, Family Planning, VIAC (cervical cancer screening), Antenatal care, Postnatal care, General Surgery, Internal Medicine, Pediatrics, Orthopedics, Ophthalmology, Dental Services, Pharmacy, Laboratory Services, X-Ray, Ultrasound'
            ],
            [
                'name' => 'United Bulawayo Hospitals (UBH)',
                'address' => 'Corner 12th Avenue, Bulawayo',
                'latitude' => -20.146789,
                'longitude' => 28.557545,
                'has_icu' => true,
                'has_trauma' => true,
                'has_maternity' => true,
                'other_services' => 'Lady Rodwell Maternity, Richard Morris Hospital (pediatrics), Casualty (24/7 Emergency), Internal Medicine, General Surgery, Orthopedics, Ophthalmology, ENT, Neurosurgery, Urology, Obstetrics & Gynaecology, HIV/AIDS Management, Palliative Care, Mental Health Services, Cervical Cancer Screening (VIAC), Diagnostic Laboratory, Haematology, Biochemistry, X-Ray, Physiotherapy, Pharmacy'
            ],
            [
                'name' => 'Mater Dei Hospital',
                'address' => '10th Avenue, Hillside, Bulawayo',
                'latitude' => -20.182078,
                'longitude' => 28.597066,
                'has_icu' => true,
                'has_trauma' => true,
                'has_maternity' => true,
                'other_services' => 'Renal Dialysis Unit (18 beds), Retail Pharmacy, Specialist Consultation Rooms, Family Practitioners, Orthopedic Department, Well-Women Clinic, Antenatal Clinic, Postnatal Clinic, Well Baby Clinic, Immunizations, Audiology Unit, Dental Clinic, Doctors Suites, COVID-19 Centre, Spiritual Center, Psychological Services'
            ],
            [
                'name' => 'Nkulumane Clinic',
                'address' => 'Nkulumane, Bulawayo',
                'latitude' => -20.188798,
                'longitude' => 28.520,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => true,
                'other_services' => 'Primary Healthcare, Antenatal Care, Postnatal Care, Immunizations, Family Planning, HIV Testing & Counseling, Minor ailments treatment, Pharmacy services, Chronic disease management (Hypertension, Diabetes), Growth monitoring'
            ],
            [
                'name' => 'Thorngrove Infectious Diseases Hospital',
                'address' => 'Thorngrove, Bulawayo',
                'latitude' => -20.146789,
                'longitude' => 28.557545,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => false,
                'other_services' => 'Infectious Disease Management, Tuberculosis (TB) Treatment, Isolation Facilities, HIV/AIDS Care, Epidemic Preparedness, Quarantine Services, Public Health Surveillance, Contact Tracing, Infectious Disease Research'
            ],
            [
                'name' => 'Pelandaba Clinic',
                'address' => 'Pelandaba, Bulawayo',
                'latitude' => -20.146981,
                'longitude' => 28.527493,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => true,
                'other_services' => 'Primary Healthcare, Antenatal Care, Postnatal Care, Immunizations, Family Planning, HIV Testing & Counseling, Minor ailments, Pharmacy, Chronic disease management, Growth monitoring, Maternity waiting home'
            ],
            [
                'name' => 'Hillside Clinic',
                'address' => 'Hillside, Bulawayo',
                'latitude' => -20.185684,
                'longitude' => 28.609985,
                'has_icu' => false,
                'has_trauma' => true,
                'has_maternity' => false,
                'other_services' => 'Outpatient Pharmacy, Minor Trauma Care, Primary Healthcare, HIV Testing & Counseling, Chronic Disease Management, General Consultations, Laboratory Services, Wound Care, Injections & Vaccinations, Health Screening, Family Planning'
            ],
            [
                'name' => 'Royal Women’s Clinic',
                'address' => 'Bulawayo',
                'latitude' => -20.19217,
                'longitude' => 28.60122,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => true,
                'other_services' => 'Women’s Health Services, Antenatal Care, Postnatal Care, Family Planning, Cervical Cancer Screening, Breast Examinations, Gynecological Consultations, Menopause Management, Fertility Counseling, Prenatal Classes, Ultrasound (Obstetric), HIV Testing for Pregnant Women, PMTCT'
            ],
            [
                'name' => 'Premier Hillside Hospital',
                'address' => 'Hillside, Bulawayo',
                'latitude' => -20.14663,
                'longitude' => 28.58830,
                'has_icu' => true,
                'has_trauma' => true,
                'has_maternity' => true,
                'other_services' => 'Private Hospital Services, Maternity Care, General Surgery, Internal Medicine, Pediatrics, Orthopedics, Ophthalmology, Pharmacy, Laboratory, X-Ray, Ultrasound, Physiotherapy, Dental Services, Specialist Consultations, 24-Hour Emergency'
            ],
            [
                'name' => 'Emergency Medical Clinic',
                'address' => 'Bulawayo',
                'latitude' => -20.15810,
                'longitude' => 28.58814,
                'has_icu' => false,
                'has_trauma' => true,
                'has_maternity' => false,
                'other_services' => '24-Hour Emergency Services, Trauma Care, Urgent Care, Minor Surgery, Wound Management, Fracture Care, Emergency Stabilization, Ambulance Services, Emergency Triage, Acute Illness Management, After-Hours Care'
            ],
            [
                'name' => 'Ingutsheni Central Hospital',
                'address' => 'Bulawayo',
                'latitude' => -20.1468,
                'longitude' => 28.5872,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => false,
                'other_services' => 'Psychiatric Hospital, Mental Health Services, Inpatient Psychiatric Care, Outpatient Mental Health Clinics, Substance Abuse Treatment, Psychiatric Rehabilitation, Child & Adolescent Mental Health, Forensic Psychiatry, Counseling Services, Occupational Therapy, Psychology Services'
            ],
            [
                'name' => 'St. Luke\'s Hospital',
                'address' => 'Lobengula Street, Bulawayo',
                'latitude' => -20.1583,
                'longitude' => 28.5817,
                'has_icu' => false,
                'has_trauma' => true,
                'has_maternity' => false,
                'other_services' => 'Mission Hospital, General Medical Services, Outpatient Department, HIV/AIDS Care, Tuberculosis Treatment, Maternal & Child Health, Immunizations, Family Planning, Health Education, Community Outreach, Pharmacy, Laboratory'
            ],
            [
                'name' => 'Bulawayo Central Hospital',
                'address' => 'Central Business District, Bulawayo',
                'latitude' => -20.1525,
                'longitude' => 28.5875,
                'has_icu' => false,
                'has_trauma' => true,
                'has_maternity' => false,
                'other_services' => 'Private Hospital, General Practice, Occupational Health, Travel Medicine, Executive Health Screenings, Minor Surgery, Chronic Disease Management, Specialist Referrals, Pharmacy, Laboratory, Physiotherapy'
            ],
            [
                'name' => 'Ekhuselweni Clinic',
                'address' => 'Njube, Bulawayo',
                'latitude' => -20.1750,
                'longitude' => 28.5350,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => true,
                'other_services' => 'Primary Healthcare, Antenatal Care, Postnatal Care, Immunizations, Family Planning, HIV Testing & Counseling, PMTCT, TB Screening, Growth Monitoring, Nutrition Programs, Health Education'
            ],
            [
                'name' => 'Nketa 8 Clinic',
                'address' => 'Nketa 8, Bulawayo',
                'latitude' => -20.2150,
                'longitude' => 28.5400,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => true,
                'other_services' => 'Primary Healthcare, Maternity Services, Antenatal Clinic, Postnatal Care, Immunizations, Family Planning, HIV Services, TB Screening, Chronic Disease Management, Minor Ailments Treatment, Pharmacy'
            ],
            [
                'name' => 'Emganwini Clinic',
                'address' => 'Emganwini, Bulawayo',
                'latitude' => -20.2050,
                'longitude' => 28.5250,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => true,
                'other_services' => 'Primary Healthcare, Maternity Services, Antenatal Care, Postnatal Care, Well Baby Clinic, Immunizations, Family Planning, HIV Counseling & Testing, TB Screening, Chronic Disease Management, Minor Ailments, Pharmacy'
            ],
            [
                'name' => 'Mahatshula Clinic',
                'address' => 'Mahatshula, Bulawayo',
                'latitude' => -20.1400,
                'longitude' => 28.5650,
                'has_icu' => false,
                'has_trauma' => false,
                'has_maternity' => true,
                'other_services' => 'Primary Healthcare, Maternity Services, Antenatal Clinic, Postnatal Care, Immunizations, Family Planning, HIV Services, TB Screening, Chronic Disease Management (Hypertension, Diabetes, Asthma), Minor Ailments, Pharmacy'
            ]
        ];

        // Assign phone numbers and insert
        foreach ($facilities as $index => $facility) {
            $phoneIndex = $index % count($phones);
            $facility['phone'] = $phones[$phoneIndex];
            $facility['last_updated_at'] = now();
            $facility['created_at'] = now();
            $facility['updated_at'] = now();
            
            $id = DB::table('facilities')->insertGetId($facility);
            
            // Update geometry column for PostGIS
            DB::statement("
                UPDATE facilities 
                SET geom = ST_SetSRID(ST_MakePoint({$facility['longitude']}, {$facility['latitude']}), 4326)
                WHERE id = {$id}
            ");
            
            $this->command->info("Added: {$facility['name']}");
        }
        
        $this->command->info('====================================');
        $this->command->info('✅ All ' . count($facilities) . ' facilities seeded successfully!');
        $this->command->info('====================================');
    }
}