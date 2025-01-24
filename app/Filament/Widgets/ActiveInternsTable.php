<?php

namespace App\Filament\Widgets;

use App\Models\Intern;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ActiveInternsTable extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public function query(): Builder
    {
        return Intern::query()
            ->whereHas('internships', function ($query) {
                $query->where('status', 'active');
            });
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->query())
            ->heading('Estagiários Ativos')
            ->description('Lista de todos os estagiários atualmente ativos')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->copyable()
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-identification'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('internships.course.name')
                    ->label('Curso')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\TextColumn::make('internships.department.acronym')
                    ->label('Setor')
                    ->searchable()
                    ->copyable()
                    ->badge(),
                Tables\Columns\TextColumn::make('internships.supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-user'),
            ])
            ->striped()
            ->paginated([5]);
    }
}
