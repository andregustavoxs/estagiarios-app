<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($action) {
                    if ($this->record->interns()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Ação bloqueada')
                            ->body('Não é possível excluir este setor pois existem estagiários vinculados a ele.')
                            ->send();
                        
                        $action->cancel();
                    }
                }),
        ];
    }
}
