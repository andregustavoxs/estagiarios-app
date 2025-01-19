<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CoursesNearLimitTable extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Course::query()
                    ->whereRaw('(
                        SELECT COUNT(*) 
                        FROM internships 
                        WHERE internships.course_id = courses.id
                    ) >= courses.vacancies * 0.8')
            )
            ->heading('Cursos Próximos do Limite')
            ->description('Cursos com 80% ou mais das vagas preenchidas')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome do Curso')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vacancies')
                    ->label('Total de Vagas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vacancies_used')
                    ->label('Vagas Ocupadas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vacancies_available')
                    ->label('Vagas Disponíveis')
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_percentage')
                    ->label('Ocupação')
                    ->state(function (Course $record): float {
                        $used = $record->vacancies_used;
                        $total = $record->vacancies;
                        return ($used / $total) * 100;
                    })
                    ->suffix('%')
                    ->color(function (Course $record): string {
                        $percentage = ($record->vacancies_used / $record->vacancies) * 100;
                        if ($percentage >= 90) return 'danger';
                        if ($percentage >= 80) return 'warning';
                        return 'success';
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                
            ])
            ->paginated([5]);
    }
}
