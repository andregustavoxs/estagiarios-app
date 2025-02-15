<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditInternship extends EditRecord
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Estágio arquivado')
                        ->body('O estágio foi movido para a lixeira.')
                ),
            Actions\ForceDeleteAction::make()
                ->requiresConfirmation()
                ->before(function () {
                    // Check if internship has an intern (including soft deleted ones)
                    if ($this->record->intern()->withTrashed()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Não é possível excluir')
                            ->body('Este estágio possui um estagiário vinculado (ativo ou arquivado). Remova o estagiário primeiro.')
                            ->send();

                        $this->halt();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Estágio excluído')
                        ->body('O estágio foi excluído permanentemente.')
                ),
            Actions\RestoreAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Estágio restaurado')
                        ->body('O estágio foi restaurado com sucesso.')
                ),
        ];
    }

    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
