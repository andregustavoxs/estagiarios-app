<?php

namespace App\Filament\Resources\InternResource\Pages;

use App\Filament\Resources\InternResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditIntern extends EditRecord
{
    protected static string $resource = InternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Estagiário arquivado')
                        ->body('O estagiário foi movido para a lixeira.')
                ),
            Actions\ForceDeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription(function (Actions\ForceDeleteAction $action): string {
                    $internshipsCount = $this->record->internships()->withTrashed()->count();

                    if ($internshipsCount > 0) {
                        $warning = $internshipsCount === 1
                            ? 'Atenção: 1 estágio também será excluído permanentemente.'
                            : "Atenção: {$internshipsCount} estágios também serão excluídos permanentemente.";

                        return "Tem certeza que deseja excluir permanentemente este estagiário?\n\n{$warning}";
                    }

                    return 'Tem certeza que deseja excluir permanentemente este estagiário?';
                })
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('warning')
                ->modalHeading('Excluir Permanentemente')
                ->closeModalByClickingAway(false)
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Estagiário excluído')
                        ->body('O estagiário e seus estágios foram excluídos permanentemente.')
                ),
            Actions\RestoreAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Estagiário restaurado')
                        ->body('O estagiário foi restaurado com sucesso.')
                ),
        ];
    }

    public function getTitle(): string
    {
        return 'Editar Estagiário';
    }

    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
