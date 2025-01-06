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
                'name' => 'Departamento de Tecnologia da Informação',
                'acronym' => 'DTI',
            ],
            [
                'name' => 'Departamento de Recursos Humanos',
                'acronym' => 'DRH',
            ],
            [
                'name' => 'Departamento Financeiro',
                'acronym' => 'DFI',
            ],
            [
                'name' => 'Departamento de Marketing',
                'acronym' => 'DMK',
            ],
            [
                'name' => 'Departamento de Operações',
                'acronym' => 'DOP',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
