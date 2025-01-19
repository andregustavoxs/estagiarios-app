<?php

namespace App\Filament\Widgets;

use App\Models\Intern;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class InternsOnVacationTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Intern::query()
                    ->whereHas('internships', function ($query) {
                        $query->whereHas('vacations', function ($query) {
                            $query->whereDate('start_date', '<=', now())
                                ->whereDate('end_date', '>=', now());
                        });
                    })
            )
            ->heading('Estagiários em Férias')
            ->description('Lista de estagiários que estão atualmente em férias')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internships.department.name')
                    ->label('Setor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internships.vacations.start_date')
                    ->label('Início das Férias')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('internships.vacations.end_date')
                    ->label('Fim das Férias')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->paginated([5]);
    }
}
