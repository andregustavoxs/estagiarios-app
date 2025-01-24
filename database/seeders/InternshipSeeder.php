<?php

namespace Database\Seeders;

use App\Models\Internship;
use App\Models\Intern;
use App\Models\Course;
use App\Models\Department;
use App\Models\Supervisor;
use App\Models\EducationalInstitution;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InternshipSeeder extends Seeder
{
    public function run(): void
    {
        $interns = Intern::all();
        $courses = Course::all();
        $departments = Department::all();
        $supervisors = Supervisor::all();
        $institutions = EducationalInstitution::all();

        $internships = [
            [
                'registration_number' => '2025001',
                'intern_id' => $interns[0]->id,
                'course_id' => $courses[0]->id,
                'department_id' => $departments[0]->id,
                'supervisor_id' => $supervisors[0]->id,
                'educational_institution_id' => $institutions[0]->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addYears(2),
                'status' => 'active',
            ],
            [
                'registration_number' => '2025002',
                'intern_id' => $interns[1]->id,
                'course_id' => $courses[1]->id,
                'department_id' => $departments[1]->id,
                'supervisor_id' => $supervisors[1]->id,
                'educational_institution_id' => $institutions[1]->id,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addYears(1)->addMonths(10),
                'status' => 'active',
            ],
            [
                'registration_number' => '2025003',
                'intern_id' => $interns[2]->id,
                'course_id' => $courses[2]->id,
                'department_id' => $departments[2]->id,
                'supervisor_id' => $supervisors[2]->id,
                'educational_institution_id' => $institutions[2]->id,
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addYears(2),
                'status' => 'active',
            ],
            [
                'registration_number' => '2025004',
                'intern_id' => $interns[3]->id,
                'course_id' => $courses[3]->id,
                'department_id' => $departments[3]->id,
                'supervisor_id' => $supervisors[3]->id,
                'educational_institution_id' => $institutions[3]->id,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addYears(1)->addMonths(9),
                'status' => 'active',
            ],
            [
                'registration_number' => '2025005',
                'intern_id' => $interns[4]->id,
                'course_id' => $courses[4]->id,
                'department_id' => $departments[4]->id,
                'supervisor_id' => $supervisors[4]->id,
                'educational_institution_id' => $institutions[4]->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addYears(2),
                'status' => 'active',
            ],
            [
                'registration_number' => '2025006',
                'intern_id' => $interns[5]->id,
                'course_id' => $courses[5]->id,
                'department_id' => $departments[5]->id,
                'supervisor_id' => $supervisors[5]->id,
                'educational_institution_id' => $institutions[5]->id,
                'start_date' => Carbon::now()->subMonths(4),
                'end_date' => Carbon::now()->addYears(1)->addMonths(8),
                'status' => 'inactive',
            ],
            [
                'registration_number' => '2025007',
                'intern_id' => $interns[6]->id,
                'course_id' => $courses[6]->id,
                'department_id' => $departments[6]->id,
                'supervisor_id' => $supervisors[6]->id,
                'educational_institution_id' => $institutions[6]->id,
                'start_date' => Carbon::now()->subMonths(5),
                'end_date' => Carbon::now()->addYears(1)->addMonths(7),
                'status' => 'active',
            ],
            [
                'registration_number' => '2025008',
                'intern_id' => $interns[7]->id,
                'course_id' => $courses[7]->id,
                'department_id' => $departments[7]->id,
                'supervisor_id' => $supervisors[7]->id,
                'educational_institution_id' => $institutions[7]->id,
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addYears(1)->addMonths(6),
                'status' => 'inactive',
            ],
            [
                'registration_number' => '2025009',
                'intern_id' => $interns[8]->id,
                'course_id' => $courses[8]->id,
                'department_id' => $departments[8]->id,
                'supervisor_id' => $supervisors[8]->id,
                'educational_institution_id' => $institutions[8]->id,
                'start_date' => Carbon::now()->subMonths(7),
                'end_date' => Carbon::now()->addYears(1)->addMonths(5),
                'status' => 'active',
            ],
            [
                'registration_number' => '2025010',
                'intern_id' => $interns[9]->id,
                'course_id' => $courses[9]->id,
                'department_id' => $departments[9]->id,
                'supervisor_id' => $supervisors[9]->id,
                'educational_institution_id' => $institutions[9]->id,
                'start_date' => Carbon::now()->subMonths(8),
                'end_date' => Carbon::now()->addYears(1)->addMonths(4),
                'status' => 'active',
            ],
        ];

        foreach ($internships as $internship) {
            Internship::create($internship);
        }
    }
}
