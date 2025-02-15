<?php

namespace App\Filament\Resources\InternshipAgencyResource\Pages;

use App\Filament\Resources\InternshipAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternshipAgency extends EditRecord
{
    protected static string $resource = InternshipAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
