<?php

namespace Database\Seeders;

use App\Models\Internship;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InternshipSeeder extends Seeder
{
    public function run(): void
    {
        $internships = [
            [
                'registration_number' => '2025001',
                'intern_id' => 1,
                'course_id' => 5, // Sistemas de Informação
                'department_id' => 1, // SETIN
                'supervisor_id' => 1,
                'internship_agency_id' => 1,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
            ],
            [
                'registration_number' => '2025002',
                'intern_id' => 2,
                'course_id' => 1, // Administração
                'department_id' => 2, // SECAD
                'supervisor_id' => 2,
                'internship_agency_id' => 1,
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(6),
            ],
            [
                'registration_number' => '2025003',
                'intern_id' => 3,
                'course_id' => 3, // Direito
                'department_id' => 3, // SEFIS
                'supervisor_id' => 3,
                'internship_agency_id' => 2,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(10),
            ],
            [
                'registration_number' => '2025004',
                'intern_id' => 4,
                'course_id' => 2, // Ciências Contábeis
                'department_id' => 4, // SECEX
                'supervisor_id' => 4,
                'internship_agency_id' => 2,
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(11),
            ],
            [
                'registration_number' => '2025005',
                'intern_id' => 5,
                'course_id' => 1, // Administração
                'department_id' => 5, // SEGEP
                'supervisor_id' => 5,
                'internship_agency_id' => 3,
                'start_date' => Carbon::now()->subMonths(4),
                'end_date' => Carbon::now()->addMonths(8),
            ],
        ];

        foreach ($internships as $internship) {
            Internship::create($internship);
        }
    }
}
