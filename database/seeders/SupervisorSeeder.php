<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use App\Models\Department;
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

        $departments = Department::all();

        $supervisors = [
            [
                'name' => 'Carlos Roberto Silva',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
                'department_id' => $departments[0]->id,
            ],
            [
                'name' => 'Maria Fernanda Santos',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
                'department_id' => $departments[1]->id,
            ],
            [
                'name' => 'Pedro Henrique Costa',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
                'department_id' => $departments[2]->id,
            ],
            [
                'name' => 'Ana Beatriz Lima',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
                'department_id' => $departments[3]->id,
            ],
            [
                'name' => 'Lucas Oliveira Martins',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
                'department_id' => $departments[4]->id,
            ],
            [
                'name' => 'Patricia Santos Costa',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
                'department_id' => $departments[5]->id,
            ],
            [
                'name' => 'Roberto Carlos Lima',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
                'department_id' => $departments[6]->id,
            ],
            [
                'name' => 'Fernanda Oliveira Silva',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
                'department_id' => $departments[7]->id,
            ],
            [
                'name' => 'Marcelo Henrique Santos',
                'photo' => $malePhotos[$maleIndex++]['picture']['large'],
                'department_id' => $departments[8]->id,
            ],
            [
                'name' => 'Camila Lima Pereira',
                'photo' => $femalePhotos[$femaleIndex++]['picture']['large'],
                'department_id' => $departments[9]->id,
            ],
        ];

        foreach ($supervisors as $supervisor) {
            Supervisor::create($supervisor);
        }
    }
}
