<?php

namespace App\Filament\Resources\InternResource\Pages;

use App\Filament\Resources\InternResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditIntern extends EditRecord
{
    protected static string $resource = InternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($action) {
                    if ($this->record->internships()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Ação bloqueada')
                            ->body('Não é possível excluir este estagiário pois existem estágios vinculados a ele.')
                            ->send();
                        
                        $action->cancel();
                    }
                }),
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
