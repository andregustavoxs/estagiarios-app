<?php

namespace App\Filament\Resources\SupervisorResource\Pages;

use App\Filament\Resources\SupervisorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSupervisor extends EditRecord
{
    protected static string $resource = SupervisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($action) {
                    if ($this->record->interns()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Ação bloqueada')
                            ->body('Não é possível excluir este supervisor pois existem estagiários vinculados a ele.')
                            ->send();
                        
                        $action->cancel();
                    }
                }),
        ];
    }
}
