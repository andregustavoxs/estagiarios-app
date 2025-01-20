<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CourseSeeder::class,
            DepartmentSeeder::class,
            InternshipAgencySeeder::class,
            SupervisorSeeder::class,
            InternSeeder::class,
            InternshipSeeder::class,
            UserSeeder::class,
        ]);
    }
}
