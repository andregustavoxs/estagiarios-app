<?php

namespace Database\Seeders;

use App\Models\Intern;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class InternSeeder extends Seeder
{
    public function run(): void
    {
        // Get male photos
        $responseMale = Http::get('https://randomuser.me/api/?results=5&gender=male&nat=br');
        $malePhotos = $responseMale->json()['results'];

        // Get female photos
        $responseFemale = Http::get('https://randomuser.me/api/?results=5&gender=female&nat=br');
        $femalePhotos = $responseFemale->json()['results'];

        $maleIndex = 0;
        $femaleIndex = 0;

        $interns = [
            [
                'name' => 'Lucas Silva Santos',
                'email' => 'lucas.santos@email.com',
                'phone' => '(11) 98765-4321',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Marina Oliveira Costa',
                'email' => 'marina.costa@email.com',
                'phone' => '(11) 98765-4322',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Pedro Henrique Lima',
                'email' => 'pedro.lima@email.com',
                'phone' => '(11) 98765-4323',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Ana Carolina Souza',
                'email' => 'ana.souza@email.com',
                'phone' => '(11) 98765-4324',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Gabriel Martins Pereira',
                'email' => 'gabriel.pereira@email.com',
                'phone' => '(11) 98765-4325',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Juliana Costa Silva',
                'email' => 'juliana.silva@email.com',
                'phone' => '(11) 98765-4326',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Rafael Santos Lima',
                'email' => 'rafael.lima@email.com',
                'phone' => '(11) 98765-4327',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Beatriz Oliveira Santos',
                'email' => 'beatriz.santos@email.com',
                'phone' => '(11) 98765-4328',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Thiago Costa Lima',
                'email' => 'thiago.lima@email.com',
                'phone' => '(11) 98765-4329',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Carolina Silva Martins',
                'email' => 'carolina.martins@email.com',
                'phone' => '(11) 98765-4330',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
        ];

        foreach ($interns as $intern) {
            Intern::create($intern);
        }
    }
}
