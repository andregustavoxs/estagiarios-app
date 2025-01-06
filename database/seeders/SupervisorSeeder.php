<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use Illuminate\Database\Seeder;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        $supervisors = [
            [
                'name' => 'Ana Silva',
            ],
            [
                'name' => 'Carlos Santos',
            ],
            [
                'name' => 'Mariana Oliveira',
            ],
            [
                'name' => 'Pedro Costa',
            ],
            [
                'name' => 'Juliana Lima',
            ],
        ];

        foreach ($supervisors as $supervisor) {
            Supervisor::create($supervisor);
        }
    }
}
