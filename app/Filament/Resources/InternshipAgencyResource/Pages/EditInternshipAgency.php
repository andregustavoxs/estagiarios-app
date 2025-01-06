<?php

namespace App\Filament\Resources\InternshipAgencyResource\Pages;

use App\Filament\Resources\InternshipAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditInternshipAgency extends EditRecord
{
    protected static string $resource = InternshipAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($action) {
                    if ($this->record->interns()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Ação bloqueada')
                            ->body('Não é possível excluir este agente de integração pois existem estagiários vinculados a ele.')
                            ->send();
                        
                        $action->cancel();
                    }
                }),
        ];
    }
}
