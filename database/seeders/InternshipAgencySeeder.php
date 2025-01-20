<?php

namespace Database\Seeders;

use App\Models\InternshipAgency;
use Illuminate\Database\Seeder;

class InternshipAgencySeeder extends Seeder
{
    public function run(): void
    {
        $agencies = [
            [
                'cnpj' => '12.345.678/0001-90',
                'company_name' => 'Instituto de Educação e Cidadania do Maranhão',
                'trade_name' => 'IEC-MA',
                'phone' => '(98) 3221-5678',
                'contact_person' => 'Maria Silva',
            ],
            [
                'cnpj' => '98.765.432/0001-21',
                'company_name' => 'Centro de Integração Empresa-Escola do Maranhão',
                'trade_name' => 'CIEE-MA',
                'phone' => '(98) 3232-4321',
                'contact_person' => 'João Santos',
            ],
            [
                'cnpj' => '45.678.901/0001-34',
                'company_name' => 'Associação de Ensino Superior do Maranhão',
                'trade_name' => 'AESMA',
                'phone' => '(98) 3219-8765',
                'contact_person' => 'Ana Oliveira',
            ],
        ];

        foreach ($agencies as $agency) {
            InternshipAgency::create($agency);
        }
    }
}
