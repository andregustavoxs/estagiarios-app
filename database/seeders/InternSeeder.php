<?php

namespace Database\Seeders;

use App\Models\Intern;
use Illuminate\Database\Seeder;

class InternSeeder extends Seeder
{
    public function run(): void
    {
        $interns = [
            [
                'name' => 'João Pedro Silva',
                'email' => 'joao.silva@email.com',
                'phone' => '(98) 98888-1111',
                'photo' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Maria Clara Santos',
                'email' => 'maria.santos@email.com',
                'phone' => '(98) 98888-2222',
                'photo' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Pedro Lucas Costa',
                'email' => 'pedro.costa@email.com',
                'phone' => '(98) 98888-3333',
                'photo' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Ana Luiza Oliveira',
                'email' => 'ana.oliveira@email.com',
                'phone' => '(98) 98888-4444',
                'photo' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Lucas Gabriel Martins',
                'email' => 'lucas.martins@email.com',
                'phone' => '(98) 98888-5555',
                'photo' => null,
                'status' => 'active',
            ],
        ];

        foreach ($interns as $intern) {
            Intern::create($intern);
        }
    }
}
