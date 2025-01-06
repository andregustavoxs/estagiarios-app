<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Análise e Desenvolvimento de Sistemas',
                'vacancies' => 5,
            ],
            [
                'name' => 'Administração',
                'vacancies' => 3,
            ],
            [
                'name' => 'Ciências Contábeis',
                'vacancies' => 2,
            ],
            [
                'name' => 'Marketing Digital',
                'vacancies' => 4,
            ],
            [
                'name' => 'Engenharia de Software',
                'vacancies' => 3,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
