<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            CourseSeeder::class,
            SupervisorSeeder::class,
            EducationalInstitutionSeeder::class,
            InternSeeder::class,
            InternshipSeeder::class,
            UserSeeder::class,
        ]);
    }
}
