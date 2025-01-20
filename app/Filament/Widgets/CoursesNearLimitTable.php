<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CoursesNearLimitTable extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Course::query()->nearLimit())
            ->heading('Cursos Próximos do Limite')
            ->description('Cursos com 50% ou mais das vagas preenchidas')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome do Curso')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\TextColumn::make('vacancies')
                    ->label('Total de Vagas')
                    ->sortable()
                    ->icon('heroicon-m-squares-plus'),
                Tables\Columns\TextColumn::make('vacancies_used')
                    ->label('Vagas Ocupadas')
                    ->sortable()
                    ->icon('heroicon-m-user-group'),
                Tables\Columns\TextColumn::make('vacancies_available')
                    ->label('Vagas Disponíveis')
                    ->sortable()
                    ->icon('heroicon-m-square-3-stack-3d'),
                Tables\Columns\TextColumn::make('usage_percentage')
                    ->label('Ocupação')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1))
                    ->suffix('%')
                    ->color(function (Course $record): string {
                        if ($record->usage_percentage >= 75) return 'danger';     // 75% or more = Red
                        if ($record->usage_percentage >= 60) return 'warning';    // 60-74% = Yellow
                        return 'success';                                         // 50-59% = Green
                    })
                    ->icon('heroicon-m-chart-bar')
                    ->alignEnd()
                    ->weight('bold'),
            ])
            ->striped()
            ->paginated([5]);
    }
}
