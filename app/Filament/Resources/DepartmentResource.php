<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $modelLabel = 'Setor';
    
    protected static ?string $pluralModelLabel = 'Setores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Setor')
                    ->description('Dados de identificação do setor')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome do Setor')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite o nome completo do setor')
                                    ->helperText('Nome oficial do setor')
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este nome de setor já está em uso.',
                                    ]),

                                Forms\Components\TextInput::make('acronym')
                                    ->label('Sigla')
                                    ->required()
                                    ->maxLength(10)
                                    ->placeholder('Ex: RH, TI, ADM')
                                    ->helperText('Sigla ou abreviação do setor')
                                    ->prefixIcon('heroicon-o-identification')
                                    ->formatStateUsing(fn (?string $state): string => $state ? strtoupper($state) : '')
                                    ->dehydrateStateUsing(fn (?string $state): string => $state ? strtoupper($state) : '')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Esta sigla já está em uso.',
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-building-office'),
                Tables\Columns\TextColumn::make('acronym')
                    ->label('Sigla')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('interns_count')
                    ->label('Qtd. Estagiários')
                    ->counts('interns')
                    ->sortable()
                    ->icon('heroicon-m-user-group')
                    ->alignEnd(),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($action, Department $record) {
                        if ($record->interns()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Ação bloqueada')
                                ->body('Não é possível excluir este setor pois existem estagiários vinculados a ele.')
                                ->send();
                            
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($action, Collection $records) {
                            foreach ($records as $record) {
                                if ($record->interns()->count() > 0) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Ação bloqueada')
                                        ->body('Não é possível excluir setores que possuem estagiários vinculados.')
                                        ->send();
                                    
                                    $action->cancel();
                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\InternsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
