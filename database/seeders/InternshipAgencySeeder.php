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
                'company_name' => 'Centro de Integração Empresa-Escola',
                'trade_name' => 'CIEE',
                'cnpj' => '61600839000155',
                'phone' => '(11) 3003-2433',
                'contact_person' => 'Maria Silva',
            ],
            [
                'company_name' => 'Instituto Euvaldo Lodi',
                'trade_name' => 'IEL',
                'cnpj' => '33938861000199',
                'phone' => '(61) 3317-9000',
                'contact_person' => 'João Santos',
            ],
            [
                'company_name' => 'Fundação Mudes',
                'trade_name' => 'MUDES',
                'cnpj' => '33647633000144',
                'phone' => '(21) 2222-1000',
                'contact_person' => 'Ana Costa',
            ],
            [
                'company_name' => 'Super Estágios Ltda',
                'trade_name' => 'Super Estágios',
                'cnpj' => '11320576000173',
                'phone' => '(31) 3222-1000',
                'contact_person' => 'Pedro Lima',
            ],
            [
                'company_name' => 'Nube Núcleo Brasileiro de Estágios',
                'trade_name' => 'NUBE',
                'cnpj' => '57118087000193',
                'phone' => '(11) 3514-9300',
                'contact_person' => 'Carla Oliveira',
            ],
        ];

        foreach ($agencies as $agency) {
            InternshipAgency::create($agency);
        }
    }
}
