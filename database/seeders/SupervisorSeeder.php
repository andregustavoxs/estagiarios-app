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
                'name' => 'Carlos Roberto Silva',
                'photo' => null,
            ],
            [
                'name' => 'Maria Fernanda Santos',
                'photo' => null,
            ],
            [
                'name' => 'Pedro Henrique Costa',
                'photo' => null,
            ],
            [
                'name' => 'Ana Beatriz Lima',
                'photo' => null,
            ],
            [
                'name' => 'Lucas Oliveira Martins',
                'photo' => null,
            ],
        ];

        foreach ($supervisors as $supervisor) {
            Supervisor::create($supervisor);
        }
    }
}
