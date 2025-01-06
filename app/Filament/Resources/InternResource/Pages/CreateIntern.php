<?php

namespace App\Filament\Resources\InternResource\Pages;

use App\Filament\Resources\InternResource;
use App\Models\Course;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateIntern extends CreateRecord
{
    protected static string $resource = InternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Salvar'),
        ];
    }

    public function getTitle(): string 
    {
        return 'Novo Estagiário';
    }

    protected function getCancelButtonLabel(): string
    {
        return 'Cancelar';
    }

    protected function getCreateButtonLabel(): string
    {
        return 'Salvar';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $course = Course::find($data['course_id']);
        
        if ($course && $course->vacancies_available <= 0) {
            Notification::make()
                ->danger()
                ->title('Sem Vagas Disponíveis')
                ->body("O curso '{$course->name}' atingiu o limite de vagas.")
                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
