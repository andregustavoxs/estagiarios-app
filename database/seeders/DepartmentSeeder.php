<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Secretaria de Tecnologia e Inovação',
                'acronym' => 'SETIN',
            ],
            [
                'name' => 'Secretaria de Administração',
                'acronym' => 'SECAD',
            ],
            [
                'name' => 'Secretaria de Fiscalização',
                'acronym' => 'SEFIS',
            ],
            [
                'name' => 'Secretaria de Controle Externo',
                'acronym' => 'SECEX',
            ],
            [
                'name' => 'Secretaria de Gestão de Pessoas',
                'acronym' => 'SEGEP',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
