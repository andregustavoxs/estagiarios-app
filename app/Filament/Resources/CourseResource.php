<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Cursos';

    protected static ?string $modelLabel = 'Curso';

    protected static ?string $pluralModelLabel = 'Cursos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::sum('vacancies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('vacancies')
                    ->label('Vagas')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(function ($state, Get $get, Forms\Components\TextInput $component) {
                        $record = $component->getRecord();
                        
                        if ($record) {
                            $currentInterns = $record->interns()->count();
                            
                            if ($state < $currentInterns) {
                                $component->state($record->vacancies);
                                Notification::make()
                                    ->danger()
                                    ->title('Operação não permitida')
                                    ->body("O número de vagas não pode ser reduzido para {$state}, pois já existem {$currentInterns} estagiários atribuídos a este curso.")
                                    ->send();
                            }
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vacancies')
                    ->label('Total de Vagas')
                    ->numeric(),
                Tables\Columns\TextColumn::make('vacancies_used')
                    ->label('Vagas Ocupadas')
                    ->numeric(),
                Tables\Columns\TextColumn::make('vacancies_available')
                    ->label('Vagas Disponíveis')
                    ->numeric()
                    ->color(fn (Course $record): string => $record->vacancies_available > 0 ? 'success' : 'danger'),
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
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($action, Course $record) {
                        if ($record->interns()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Ação bloqueada')
                                ->body('Não é possível excluir este curso pois existem estagiários vinculados a ele.')
                                ->send();
                            
                            $action->cancel();
                        }
                    })
                    ->label('Excluir'),
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
                                        ->body('Não é possível excluir cursos que possuem estagiários vinculados.')
                                        ->send();
                                    
                                    $action->cancel();
                                    return;
                                }
                            }
                        })
                        ->label('Excluir selecionados'),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }    
}
