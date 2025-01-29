<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditInternship extends EditRecord
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($action) {
                    if ($this->record->commitment_terms()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Ação bloqueada')
                            ->body('Não é possível excluir este estágio pois existem termos de compromisso vinculados a ele.')
                            ->send();
                        
                        $action->cancel();
                    }
                }),
        ];
    }
}
