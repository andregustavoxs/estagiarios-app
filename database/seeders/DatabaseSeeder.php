<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DepartmentSeeder::class,
            CourseSeeder::class,
            SupervisorSeeder::class,
            InternshipAgencySeeder::class,
            InternSeeder::class,
        ]);
    }
}
