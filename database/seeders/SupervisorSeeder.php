<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class SupervisorSeeder extends Seeder
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

        $supervisors = [
            [
                'name' => 'Carlos Roberto Silva',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Maria Fernanda Santos',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Pedro Henrique Costa',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Ana Beatriz Lima',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Lucas Oliveira Martins',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Patricia Santos Costa',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Roberto Carlos Lima',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Fernanda Oliveira Silva',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Marcelo Henrique Santos',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
            ],
            [
                'name' => 'Camila Lima Pereira',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
            ],
        ];

        foreach ($supervisors as $supervisor) {
            Supervisor::create($supervisor);
        }
    }
}
