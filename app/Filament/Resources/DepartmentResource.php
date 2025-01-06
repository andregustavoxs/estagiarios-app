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
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('acronym')
                    ->label('Sigla')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('acronym')
                    ->label('Sigla')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interns_count')
                    ->counts('interns')
                    ->label('Número de Estagiários'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
