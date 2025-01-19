<?php

namespace App\Filament\Widgets;

use App\Models\Intern;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveInternsTable extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Intern::query()->where('status', 'active')
            )
            ->heading('Estagiários Ativos')
            ->description('Lista de todos os estagiários atualmente ativos')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internships.department.name')
                    ->label('Setor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internships.supervisor.name')
                    ->label('Supervisor')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
            
            ])
            ->paginated([5]);
    }
}
