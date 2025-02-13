<?php

namespace App\Filament\Resources\EvaluationTemplateResource\Pages;

use App\Filament\Resources\EvaluationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluationTemplates extends ListRecords
{
    protected static string $resource = EvaluationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
