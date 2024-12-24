<?php

namespace App\Filament\Resources\InternResource\Pages;

use App\Filament\Resources\InternResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntern extends EditRecord
{
    protected static string $resource = InternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir'),
            Actions\ForceDeleteAction::make()
                ->label('Excluir Permanentemente'),
            Actions\RestoreAction::make()
                ->label('Restaurar'),
        ];
    }

    public function getTitle(): string 
    {
        return 'Editar Estagiário';
    }
}
