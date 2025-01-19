<?php

namespace App\Filament\Resources\InternResource\Pages;

use App\Filament\Resources\InternResource;
use Filament\Actions;
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
}
