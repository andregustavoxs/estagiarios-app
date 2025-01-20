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
                'name' => 'Administração',
                'vacancies' => 20,
            ],
            [
                'name' => 'Ciências Contábeis',
                'vacancies' => 15,
            ],
            [
                'name' => 'Direito',
                'vacancies' => 25,
            ],
            [
                'name' => 'Engenharia Civil',
                'vacancies' => 10,
            ],
            [
                'name' => 'Sistemas de Informação',
                'vacancies' => 15,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
