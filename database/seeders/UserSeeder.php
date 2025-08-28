<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin_user',
                'email' => 'admin@patientcare.local',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'username' => 'patient_user',
                'email' => 'patient1@patientcare.local',
                'password' => Hash::make('password'),
                'role' => 'patient',
            ],
            [
                'username' => 'doctor_user',
                'email' => 'doctor1@patientcare.local',
                'password' => Hash::make('password'),
                'role' => 'doctor',
            ],
            [
                'username' => 'admission_user',
                'email' => 'admit1@patientcare.local',
                'password' => Hash::make('password'),
                'role' => 'admission',
            ],
            [
                'username' => 'billing_user',
                'email' => 'billing1@patientcare.local',
                'password' => Hash::make('password'),
                'role' => 'billing',
            ],
            [
                'username' => 'hospital_user',
                'email' => 'hospital1@patientcare.local',
                'password' => Hash::make('password'),
                'role' => 'hospital_services',
            ],
        ]);
    }
}
