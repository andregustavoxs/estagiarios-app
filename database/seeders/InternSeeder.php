<?php

namespace Database\Seeders;

use App\Models\Intern;
use Illuminate\Database\Seeder;

class InternSeeder extends Seeder
{
    public function run(): void
    {
        $interns = [
            [
                'name' => 'Lucas Ferreira',
                'email' => 'lucas.ferreira@email.com',
                'phone' => '(11) 99876-5432',
                'registration_number' => '2025001',
                'course_id' => 1,
                'department_id' => 1,
                'supervisor_id' => 1,
                'internship_agency_id' => 1,
            ],
            [
                'name' => 'Beatriz Almeida',
                'email' => 'beatriz.almeida@email.com',
                'phone' => '(11) 99876-5433',
                'registration_number' => '2025002',
                'course_id' => 2,
                'department_id' => 2,
                'supervisor_id' => 2,
                'internship_agency_id' => 2,
            ],
            [
                'name' => 'Gabriel Santos',
                'email' => 'gabriel.santos@email.com',
                'phone' => '(11) 99876-5434',
                'registration_number' => '2025003',
                'course_id' => 3,
                'department_id' => 3,
                'supervisor_id' => 3,
                'internship_agency_id' => 3,
            ],
            [
                'name' => 'Isabella Costa',
                'email' => 'isabella.costa@email.com',
                'phone' => '(11) 99876-5435',
                'registration_number' => '2025004',
                'course_id' => 4,
                'department_id' => 4,
                'supervisor_id' => 4,
                'internship_agency_id' => 4,
            ],
            [
                'name' => 'Matheus Lima',
                'email' => 'matheus.lima@email.com',
                'phone' => '(11) 99876-5436',
                'registration_number' => '2025005',
                'course_id' => 5,
                'department_id' => 5,
                'supervisor_id' => 5,
                'internship_agency_id' => 5,
            ],
        ];

        foreach ($interns as $intern) {
            Intern::create($intern);
        }
    }
}
