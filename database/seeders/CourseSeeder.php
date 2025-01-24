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
                'name' => 'Ciência da Computação',
                'vacancies' => 5,
            ],
            [
                'name' => 'Engenharia de Software',
                'vacancies' => 3,
            ],
            [
                'name' => 'Administração',
                'vacancies' => 4,
            ],
            [
                'name' => 'Contabilidade',
                'vacancies' => 3,
            ],
            [
                'name' => 'Marketing',
                'vacancies' => 2,
            ],
            [
                'name' => 'Direito',
                'vacancies' => 3,
            ],
            [
                'name' => 'Engenharia de Produção',
                'vacancies' => 4,
            ],
            [
                'name' => 'Sistemas de Informação',
                'vacancies' => 3,
            ],
            [
                'name' => 'Recursos Humanos',
                'vacancies' => 2,
            ],
            [
                'name' => 'Logística',
                'vacancies' => 3,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
