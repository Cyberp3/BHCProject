<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\HealthAssessment;
use App\Models\HealthServiceRecord;
use App\Models\Patient;
use App\Models\Purok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoPatientSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $nurse = User::where('role', 'nurse')->first() ?? $admin;
        $midwife = User::where('role', 'midwife')->first() ?? $admin;
        $bns = User::where('role', 'bns')->first() ?? $admin;
        $bhw = User::where('role', 'bhw')->first() ?? $admin;

        $puroks = Purok::all()->keyBy('id');
        if ($puroks->isEmpty()) {
            return;
        }

        // 1. Juan Dela Cruz - Adult Hypertensive (CVD Screening)
        $p1 = Patient::create([
            'patient_control_number' => 'TAC-2026-0001',
            'first_name' => 'Juan',
            'middle_name' => 'Mercado',
            'last_name' => 'Dela Cruz',
            'sex' => 'Male',
            'date_of_birth' => '1981-04-12',
            'civil_status' => 'Married',
            'purok_id' => $puroks[3]->id ?? $puroks->first()->id,
            'street_address' => 'Km. 14, Purok 3',
            'contact_number' => '09171234567',
            'philhealth_number' => '01-234567890-1',
            'blood_type' => 'O+',
            'emergency_contact_name' => 'Maria Dela Cruz (Spouse)',
            'emergency_contact_number' => '09171234568',
            'created_by' => $bhw->id,
        ]);

        HealthAssessment::create([
            'patient_id' => $p1->id,
            'user_id' => $nurse->id,
            'assessment_date' => Carbon::today()->subDays(14)->format('Y-m-d'),
            'weight_kg' => 74.0,
            'height_cm' => 168.0,
            'systolic_bp' => 140,
            'diastolic_bp' => 90,
            'pulse_rate' => 78,
            'respiratory_rate' => 18,
            'temperature_celsius' => 36.6,
            'notes' => 'Patient reports occasional morning occipital headache. Advised low-salt diet.',
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p1->id,
            'user_id' => $nurse->id,
            'service_type' => 'cvd_screening',
            'service_date' => Carbon::today()->subDays(14)->format('Y-m-d'),
            'complaint_or_reason' => 'Routine adult blood pressure check',
            'findings_and_notes' => 'Elevated BP 140/90. Stage 1 Hypertension. Prescribed Losartan 50mg OD.',
            'service_specific_data' => [
                'has_hypertension_history' => 'yes',
                'has_diabetes_history' => 'no',
                'smoker_status' => 'non_smoker',
                'systolic_bp' => 140,
                'diastolic_bp' => 90,
                'cvd_risk_level' => 'Moderate Risk (10-20%)',
            ],
            'next_follow_up_date' => Carbon::today()->format('Y-m-d'),
        ]);

        Appointment::create([
            'patient_id' => $p1->id,
            'scheduled_by' => $nurse->id,
            'service_type' => 'cvd_screening',
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'appointment_time' => '09:00',
            'purpose' => '2-week BP re-check and maintenance medication refill',
            'status' => 'scheduled',
            'status_notes' => 'Bring empty medicine blister pack and PhilHealth ID.',
        ]);

        // 2. Maria Clara Santos - Pregnant Mother (Prenatal Care)
        $p2 = Patient::create([
            'patient_control_number' => 'TAC-2026-0002',
            'first_name' => 'Maria Clara',
            'middle_name' => 'Reyes',
            'last_name' => 'Santos',
            'sex' => 'Female',
            'date_of_birth' => '1998-09-24',
            'civil_status' => 'Married',
            'purok_id' => $puroks[1]->id ?? $puroks->first()->id,
            'street_address' => 'Near Chapel, Purok 1',
            'contact_number' => '09289876543',
            'philhealth_number' => '02-345678901-2',
            'blood_type' => 'A+',
            'emergency_contact_name' => 'Crisostomo Santos (Husband)',
            'emergency_contact_number' => '09289876544',
            'created_by' => $midwife->id,
        ]);

        HealthAssessment::create([
            'patient_id' => $p2->id,
            'user_id' => $midwife->id,
            'assessment_date' => Carbon::today()->subDays(20)->format('Y-m-d'),
            'weight_kg' => 58.5,
            'height_cm' => 155.0,
            'systolic_bp' => 110,
            'diastolic_bp' => 70,
            'pulse_rate' => 74,
            'respiratory_rate' => 16,
            'temperature_celsius' => 36.5,
            'notes' => '2nd Trimester Prenatal checkup. Fetal heart tone good.',
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p2->id,
            'user_id' => $midwife->id,
            'service_type' => 'prenatal_care',
            'service_date' => Carbon::today()->subDays(20)->format('Y-m-d'),
            'complaint_or_reason' => '2nd Trimester regular follow-up',
            'findings_and_notes' => 'Gravida 1 Para 0. AOG 24 weeks. Fundic height 23cm. FHT 142 bpm. Prescribed Ferrous Sulfate with Folic Acid.',
            'service_specific_data' => [
                'gravida' => '1',
                'para' => '0',
                'lmp' => Carbon::today()->subDays(168)->format('Y-m-d'),
                'edc' => Carbon::today()->addDays(112)->format('Y-m-d'),
                'trimester' => '2nd Trimester',
                'fundic_height_cm' => 23,
                'fetal_heart_tone' => '142 bpm',
                'tetanus_toxoid_status' => 'Td 2 Given',
            ],
            'next_follow_up_date' => Carbon::today()->addDays(10)->format('Y-m-d'),
        ]);

        Appointment::create([
            'patient_id' => $p2->id,
            'scheduled_by' => $midwife->id,
            'service_type' => 'prenatal_care',
            'appointment_date' => Carbon::today()->addDays(10)->format('Y-m-d'),
            'appointment_time' => '10:00',
            'purpose' => '3rd Prenatal Checkup and Gestational Diabetes Screen',
            'status' => 'scheduled',
            'status_notes' => 'Bring maternal health record booklet (Pink book).',
        ]);

        // 3. Baby Ethan Gabriel Reyes - 6-month-old Infant (Immunization)
        $p3 = Patient::create([
            'patient_control_number' => 'TAC-2026-0003',
            'first_name' => 'Ethan Gabriel',
            'middle_name' => 'Cruz',
            'last_name' => 'Reyes',
            'sex' => 'Male',
            'date_of_birth' => Carbon::today()->subMonths(6)->format('Y-m-d'),
            'civil_status' => 'Single',
            'purok_id' => $puroks[2]->id ?? $puroks->first()->id,
            'street_address' => 'Block 4, Purok 2',
            'contact_number' => '09195554321',
            'blood_type' => 'B+',
            'emergency_contact_name' => 'Elena Reyes (Mother)',
            'emergency_contact_number' => '09195554321',
            'created_by' => $nurse->id,
        ]);

        HealthAssessment::create([
            'patient_id' => $p3->id,
            'user_id' => $nurse->id,
            'assessment_date' => Carbon::today()->subDays(30)->format('Y-m-d'),
            'weight_kg' => 7.4,
            'height_cm' => 66.0,
            'systolic_bp' => null,
            'diastolic_bp' => null,
            'pulse_rate' => 110,
            'respiratory_rate' => 30,
            'temperature_celsius' => 36.7,
            'notes' => '6-month well baby check. Active, alert, good milestones.',
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p3->id,
            'user_id' => $nurse->id,
            'service_type' => 'immunization',
            'service_date' => Carbon::today()->subDays(30)->format('Y-m-d'),
            'complaint_or_reason' => 'Routine EPI Infant Immunization',
            'findings_and_notes' => 'Administered Pentavalent Vaccine (Dose 2) left anterolateral thigh, OPV 2 oral drops.',
            'service_specific_data' => [
                'vaccine_administered' => 'Pentavalent + OPV',
                'dose_sequence' => 'Dose 2',
                'batch_lot_number' => 'LOT-2026-PENT-001',
                'site_of_injection' => 'Left vastus lateralis (IM)',
                'adverse_events' => 'None observed in 15 min monitoring',
            ],
            'next_follow_up_date' => Carbon::today()->format('Y-m-d'),
        ]);

        Appointment::create([
            'patient_id' => $p3->id,
            'scheduled_by' => $nurse->id,
            'service_type' => 'immunization',
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'appointment_time' => '08:30',
            'purpose' => 'Pentavalent Dose 3 and OPV Dose 3 + IPV injection',
            'status' => 'scheduled',
            'status_notes' => 'Bring Child Immunization Record (Yellow Card).',
        ]);

        // 4. Teresa Bautista - Senior Citizen (PhilPEN NCD Assessment)
        $p4 = Patient::create([
            'patient_control_number' => 'TAC-2026-0004',
            'first_name' => 'Teresa',
            'middle_name' => 'Aquino',
            'last_name' => 'Bautista',
            'sex' => 'Female',
            'date_of_birth' => '1962-11-05',
            'civil_status' => 'Widowed',
            'purok_id' => $puroks[5]->id ?? $puroks->first()->id,
            'street_address' => 'Purok 5, Upper Tacunan',
            'contact_number' => '09391238901',
            'philhealth_number' => '05-998877665-3',
            'blood_type' => 'O+',
            'emergency_contact_name' => 'Grace Bautista (Daughter)',
            'emergency_contact_number' => '09391238902',
            'created_by' => $bhw->id,
        ]);

        HealthAssessment::create([
            'patient_id' => $p4->id,
            'user_id' => $nurse->id,
            'assessment_date' => Carbon::today()->subDays(7)->format('Y-m-d'),
            'weight_kg' => 62.0,
            'height_cm' => 150.0,
            'systolic_bp' => 130,
            'diastolic_bp' => 85,
            'pulse_rate' => 76,
            'respiratory_rate' => 17,
            'temperature_celsius' => 36.4,
            'notes' => 'Senior citizen PhilPEN health assessment. Known Type 2 Diabetes.',
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p4->id,
            'user_id' => $nurse->id,
            'service_type' => 'philpen',
            'service_date' => Carbon::today()->subDays(7)->format('Y-m-d'),
            'complaint_or_reason' => 'Annual PhilPEN Non-Communicable Disease Risk Evaluation',
            'findings_and_notes' => 'FBS: 126 mg/dL. Moderately controlled. Refilled Metformin 500mg #60.',
            'service_specific_data' => [
                'tobacco_use' => 'Non-smoker',
                'alcohol_consumption' => 'None',
                'fasting_blood_sugar' => '126 mg/dL',
                'total_cholesterol' => '190 mg/dL',
                'risk_stratification' => 'Low to Moderate (<10%)',
            ],
            'next_follow_up_date' => Carbon::today()->addDays(23)->format('Y-m-d'),
        ]);

        Appointment::create([
            'patient_id' => $p4->id,
            'scheduled_by' => $nurse->id,
            'service_type' => 'philpen',
            'appointment_date' => Carbon::today()->addDays(23)->format('Y-m-d'),
            'appointment_time' => '09:00',
            'purpose' => 'Monthly FBS test and maintenance medication refill',
            'status' => 'scheduled',
            'status_notes' => 'Fasting 8-10 hours prior to morning blood extraction.',
        ]);

        // 5. Rosa Batungbakal - Family Planning (DMPA Injectable)
        $p5 = Patient::create([
            'patient_control_number' => 'TAC-2026-0005',
            'first_name' => 'Rosa',
            'middle_name' => 'Dimagiba',
            'last_name' => 'Batungbakal',
            'sex' => 'Female',
            'date_of_birth' => '1992-06-18',
            'civil_status' => 'Married',
            'purok_id' => $puroks[4]->id ?? $puroks->first()->id,
            'street_address' => 'Sitio Central, Purok 4',
            'contact_number' => '09187778899',
            'philhealth_number' => '04-112233445-4',
            'blood_type' => 'AB+',
            'emergency_contact_name' => 'Fernando Batungbakal (Husband)',
            'emergency_contact_number' => '09187778800',
            'created_by' => $midwife->id,
        ]);

        HealthAssessment::create([
            'patient_id' => $p5->id,
            'user_id' => $midwife->id,
            'assessment_date' => Carbon::today()->subDays(60)->format('Y-m-d'),
            'weight_kg' => 54.0,
            'height_cm' => 153.0,
            'systolic_bp' => 115,
            'diastolic_bp' => 75,
            'pulse_rate' => 72,
            'respiratory_rate' => 16,
            'temperature_celsius' => 36.5,
            'notes' => 'FP client for routine DMPA re-injection.',
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p5->id,
            'user_id' => $midwife->id,
            'service_type' => 'family_planning',
            'service_date' => Carbon::today()->subDays(60)->format('Y-m-d'),
            'complaint_or_reason' => 'Family planning renewal',
            'findings_and_notes' => 'Current user, no complaints or side effects. Administered DMPA 150mg/mL IM right deltoid.',
            'service_specific_data' => [
                'client_type' => 'Current User',
                'method_accepted' => 'DMPA (Injectable)',
                'drop_out_reason' => null,
                'source' => 'Barangay Health Center',
            ],
            'next_follow_up_date' => Carbon::today()->addDays(30)->format('Y-m-d'),
        ]);

        Appointment::create([
            'patient_id' => $p5->id,
            'scheduled_by' => $midwife->id,
            'service_type' => 'family_planning',
            'appointment_date' => Carbon::today()->addDays(30)->format('Y-m-d'),
            'appointment_time' => '11:00',
            'purpose' => 'Quarterly DMPA Injectable Re-injection',
            'status' => 'scheduled',
            'status_notes' => 'Bring Family Planning Card.',
        ]);

        // 6. Totoy Magtanggol - 3-year-old child (BNS Growth Monitoring)
        $p6 = Patient::create([
            'patient_control_number' => 'TAC-2026-0006',
            'first_name' => 'Totoy',
            'middle_name' => 'Cruz',
            'last_name' => 'Magtanggol',
            'sex' => 'Male',
            'date_of_birth' => Carbon::today()->subYears(3)->format('Y-m-d'),
            'civil_status' => 'Single',
            'purok_id' => $puroks[6]->id ?? $puroks->first()->id,
            'street_address' => 'Near Purok 6 Hall',
            'contact_number' => '09223344556',
            'blood_type' => 'O+',
            'emergency_contact_name' => 'Lourdes Magtanggol (Mother)',
            'emergency_contact_number' => '09223344556',
            'created_by' => $bns->id,
        ]);

        HealthAssessment::create([
            'patient_id' => $p6->id,
            'user_id' => $bns->id,
            'assessment_date' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'weight_kg' => 13.8,
            'height_cm' => 94.0,
            'systolic_bp' => null,
            'diastolic_bp' => null,
            'pulse_rate' => 95,
            'respiratory_rate' => 24,
            'temperature_celsius' => 36.6,
            'notes' => 'Operation Timbang (OPT) Plus quarterly child measurement.',
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p6->id,
            'user_id' => $bns->id,
            'service_type' => 'bns_program',
            'service_date' => Carbon::today()->subDays(5)->format('Y-m-d'),
            'complaint_or_reason' => 'Quarterly BNS nutrition and growth monitoring',
            'findings_and_notes' => 'Normal nutritional status for age. Administered Vitamin A capsule 200,000 IU and Albendazole 400mg deworming tablet.',
            'service_specific_data' => [
                'target_group' => 'Preschool child (12-59 months)',
                'weight_for_age' => 'Normal',
                'height_for_age' => 'Normal',
                'weight_for_length' => 'Normal',
                'vitamin_a_given' => 'Yes (200,000 IU)',
                'deworming_given' => 'Yes (Albendazole 400mg)',
            ],
            'next_follow_up_date' => Carbon::today()->addMonths(3)->format('Y-m-d'),
        ]);

        // 7. Ricardo Dalisay - NTP (Tuberculosis Screening)
        $p7 = Patient::create([
            'patient_control_number' => 'TAC-2026-0007',
            'first_name' => 'Ricardo',
            'middle_name' => 'Valdez',
            'last_name' => 'Dalisay',
            'sex' => 'Male',
            'date_of_birth' => '1985-02-14',
            'civil_status' => 'Married',
            'purok_id' => $puroks[7]->id ?? $puroks->first()->id,
            'street_address' => 'Purok 7 Riverside',
            'contact_number' => '09276543210',
            'philhealth_number' => '07-334455667-7',
            'blood_type' => 'B+',
            'emergency_contact_name' => 'Alyana Dalisay (Spouse)',
            'emergency_contact_number' => '09276543211',
            'created_by' => $nurse->id,
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p7->id,
            'user_id' => $nurse->id,
            'service_type' => 'ntp_tb',
            'service_date' => Carbon::today()->subDays(12)->format('Y-m-d'),
            'complaint_or_reason' => 'Cough for 3 weeks and afternoon chills',
            'findings_and_notes' => 'Presumptive TB evaluated. GeneXpert test performed: MTB NOT DETECTED (Negative). Diagnosed with Acute Bronchitis. Prescribed Amoxicillin 500mg TID x 7 days.',
            'service_specific_data' => [
                'tb_presumptive' => 'Yes',
                'cough_duration' => '3 weeks',
                'sputum_genexpert_result' => 'MTB Not Detected (Negative)',
                'chest_xray_findings' => 'Clear lung fields',
                'treatment_regimen' => 'Symptomatic / Antibiotics for Bronchitis',
            ],
            'next_follow_up_date' => null,
        ]);

        // 8. Elena Villanueva - Purok Kalusugan Outreach Visit
        $p8 = Patient::create([
            'patient_control_number' => 'TAC-2026-0008',
            'first_name' => 'Elena',
            'middle_name' => 'Manalo',
            'last_name' => 'Villanueva',
            'sex' => 'Female',
            'date_of_birth' => '1995-10-30',
            'civil_status' => 'Single',
            'purok_id' => $puroks[8]->id ?? $puroks->first()->id,
            'street_address' => 'Purok 8 Extension',
            'contact_number' => '09451122334',
            'blood_type' => 'A+',
            'emergency_contact_name' => 'Corazon Villanueva (Mother)',
            'emergency_contact_number' => '09451122335',
            'created_by' => $bhw->id,
        ]);

        HealthServiceRecord::create([
            'patient_id' => $p8->id,
            'user_id' => $bhw->id,
            'service_type' => 'purok_kalusugan',
            'service_date' => Carbon::today()->subDays(3)->format('Y-m-d'),
            'complaint_or_reason' => 'Purok Kalusugan community home visit',
            'findings_and_notes' => 'Household health survey completed. Environmental sanitation check (clean water source, sanitary toilet, dengue prevention/water container inspection). Health education on proper handwashing provided.',
            'service_specific_data' => [
                'activity_type' => 'Household Health Education & Sanitation Visit',
                'household_members_screened' => 4,
                'sanitation_status' => 'Satisfactory (Water sealed toilet, covered water storage)',
                'health_education_topic' => 'Dengue 4S Strategy & Hand Hygiene',
            ],
            'next_follow_up_date' => null,
        ]);
    }
}

