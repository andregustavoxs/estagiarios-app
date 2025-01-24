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
                'name' => 'Departamento de Vendas',
                'acronym' => 'DVE',
            ],
            [
                'name' => 'Departamento de Logística',
                'acronym' => 'DLG',
            ],
            [
                'name' => 'Departamento Jurídico',
                'acronym' => 'DJU',
            ],
            [
                'name' => 'Departamento de Qualidade',
                'acronym' => 'DQL',
            ],
            [
                'name' => 'Departamento de Produção',
                'acronym' => 'DPR',
            ],
            [
                'name' => 'Departamento de Pesquisa e Desenvolvimento',
                'acronym' => 'DPD',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
