<?php

namespace App\Filament\Resources\InternResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VacationsRelationManager extends RelationManager
{
    protected static string $relationship = 'vacations';
    protected static ?string $title = 'Férias';
    protected static ?string $modelLabel = 'Férias';
    protected static ?string $pluralModelLabel = 'Férias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->label('Data de Início')
                    ->required()
                    ->format('d/m/Y')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->placeholder('dd/mm/aaaa')
                    ->helperText('Data de início das férias')
                    ->prefixIcon('heroicon-o-calendar'),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Data de Término')
                    ->required()
                    ->format('d/m/Y')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->placeholder('dd/mm/aaaa')
                    ->helperText('Data de término das férias')
                    ->prefixIcon('heroicon-o-calendar'),

                Forms\Components\Textarea::make('observation')
                    ->label('Observação')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->placeholder('Digite alguma observação (opcional)')
                    ->helperText('Observações adicionais sobre as férias'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Data de Início')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Data de Término')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('isCurrentlyOnVacation')
                    ->label('Em Férias')
                    ->boolean()
                    ->state(fn ($record) => $record->isCurrentlyOnVacation())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('observation')
                    ->label('Observação')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        if (strlen($record->observation) > 50) {
                            return $record->observation;
                        }
                        return null;
                    }),
            ])
            ->filters([
                // Tables\Filters\Filter::make('current_vacation')
                //     ->label('Em Férias')
                //     ->query(fn (Builder $query): Builder => $query
                //         ->where('start_date', '<=', now())
                //         ->where('end_date', '>=', now())
                //     )
                //     ->toggle()
                //     ->default(),

                Tables\Filters\Filter::make('future_vacation')
                    ->label('Férias Futuras')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('start_date', '>', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('past_vacation')
                    ->label('Férias Passadas')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('end_date', '<', now())
                    )
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Férias'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
