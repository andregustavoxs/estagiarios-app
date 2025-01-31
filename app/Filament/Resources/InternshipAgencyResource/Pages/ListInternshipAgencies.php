<?php

namespace App\Filament\Resources\InternshipAgencyResource\Pages;

use App\Filament\Resources\InternshipAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInternshipAgencies extends ListRecords
{
    protected static string $resource = InternshipAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
