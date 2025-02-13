<?php

namespace App\Filament\Resources\EvaluationTemplateResource\Pages;

use App\Filament\Resources\EvaluationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluationTemplate extends EditRecord
{
    protected static string $resource = EvaluationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
