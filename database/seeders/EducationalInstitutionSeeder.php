<?php

namespace Database\Seeders;

use App\Models\EducationalInstitution;
use Illuminate\Database\Seeder;

class EducationalInstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = [
            [
                'company_name' => 'Universidade Federal de São Paulo',
                'trade_name' => 'UNIFESP',
                'cnpj' => '63.025.530/0001-04',
                'phone' => '(11) 5576-4848',
                'contact_person' => 'Maria Silva',
            ],
            [
                'company_name' => 'Universidade de São Paulo',
                'trade_name' => 'USP',
                'cnpj' => '63.025.530/0002-85',
                'phone' => '(11) 3091-3116',
                'contact_person' => 'João Santos',
            ],
            [
                'company_name' => 'Pontifícia Universidade Católica de São Paulo',
                'trade_name' => 'PUC-SP',
                'cnpj' => '63.025.530/0003-66',
                'phone' => '(11) 3670-8000',
                'contact_person' => 'Pedro Oliveira',
            ],
            [
                'company_name' => 'Universidade Estadual de Campinas',
                'trade_name' => 'UNICAMP',
                'cnpj' => '63.025.530/0004-47',
                'phone' => '(19) 3521-2121',
                'contact_person' => 'Ana Costa',
            ],
            [
                'company_name' => 'Universidade Estadual Paulista',
                'trade_name' => 'UNESP',
                'cnpj' => '63.025.530/0005-28',
                'phone' => '(11) 5627-0233',
                'contact_person' => 'Carlos Lima',
            ],
            [
                'company_name' => 'Universidade Federal do ABC',
                'trade_name' => 'UFABC',
                'cnpj' => '63.025.530/0006-09',
                'phone' => '(11) 4996-0001',
                'contact_person' => 'Mariana Souza',
            ],
            [
                'company_name' => 'Faculdade de Tecnologia de São Paulo',
                'trade_name' => 'FATEC',
                'cnpj' => '63.025.530/0007-90',
                'phone' => '(11) 3322-2200',
                'contact_person' => 'Roberto Pereira',
            ],
            [
                'company_name' => 'Instituto Federal de São Paulo',
                'trade_name' => 'IFSP',
                'cnpj' => '63.025.530/0008-71',
                'phone' => '(11) 3775-4500',
                'contact_person' => 'Patricia Santos',
            ],
            [
                'company_name' => 'Universidade Presbiteriana Mackenzie',
                'trade_name' => 'MACKENZIE',
                'cnpj' => '63.025.530/0009-52',
                'phone' => '(11) 2114-8000',
                'contact_person' => 'Fernando Silva',
            ],
            [
                'company_name' => 'Escola Superior de Propaganda e Marketing',
                'trade_name' => 'ESPM',
                'cnpj' => '63.025.530/0010-33',
                'phone' => '(11) 5085-4600',
                'contact_person' => 'Julia Costa',
            ],
        ];

        foreach ($institutions as $institution) {
            EducationalInstitution::create($institution);
        }
    }
}
