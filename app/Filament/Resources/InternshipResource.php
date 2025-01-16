<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternshipResource\Pages;
use App\Filament\Resources\InternshipResource\RelationManagers;
use App\Models\Course;
use App\Models\Internship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class InternshipResource extends Resource
{
    protected static ?string $model = Internship::class;
    protected static ?string $modelLabel = 'Estágio';
    protected static ?string $pluralModelLabel = 'Estágios';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Estagiário')
                    ->description('Selecione o estagiário e defina seu número de matrícula')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('intern_id')
                            ->relationship('intern', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Estagiário')
                            ->helperText('Selecione o estagiário'),

                        Forms\Components\TextInput::make('registration_number')
                            ->label('Matrícula')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ex: 2025001')
                            ->helperText('Número de matrícula único do estagiário'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Vinculação Institucional')
                    ->description('Informações sobre a vinculação do estagiário')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Curso')
                            ->relationship('course', 'name')
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $course = Course::find($state);
                                    if ($course && $course->vacancies_available <= 0) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Sem Vagas Disponíveis')
                                            ->body("O curso '{$course->name}' atingiu o limite de vagas.")
                                            ->persistent()
                                            ->send();

                                        $set('course_id', null);
                                    }
                                }
                            })
                            ->options(function () {
                                return Course::all()->mapWithKeys(function ($course) {
                                    $available = $course->vacancies_available;
                                    $suffix = $available > 0 ? " ({$available} vagas disponíveis)" : " (Sem vagas)";
                                    return [$course->id => $course->name . $suffix];
                                });
                            })
                            ->helperText('Selecione o curso do estagiário'),

                        Forms\Components\Select::make('department_id')
                            ->label('Setor')
                            ->relationship('department', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Setor onde o estágio será realizado'),

                        Forms\Components\Select::make('supervisor_id')
                            ->label('Supervisor')
                            ->relationship('supervisor', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Supervisor responsável pelo estagiário'),

                        Forms\Components\Select::make('internship_agency_id')
                            ->label('Agente de Integração')
                            ->relationship('internshipAgency', 'trade_name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Agente de integração responsável'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('intern.name')
                    ->label('Estagiário')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Setor')
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('Curso')
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('internshipAgency.trade_name')
                    ->label('Agente de Integração')
                    ->copyable()
                    ->sortable(),
            ])
            ->defaultSort('intern.name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VacationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternships::route('/'),
            'create' => Pages\CreateInternship::route('/create'),
            'edit' => Pages\EditInternship::route('/{record}/edit'),
        ];
    }
}
