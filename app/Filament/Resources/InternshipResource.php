<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternshipResource\Pages;
use App\Filament\Resources\InternshipResource\RelationManagers;
use App\Models\Course;
use App\Models\CommitmentTerm;
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
                Forms\Components\Section::make('Informações do Estágio')
                    ->description('Informações básicas do estágio')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('intern_id')
                            ->relationship('intern', 'name')
                            ->label('Estagiário')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                            ])
                            ->helperText('Selecione o estagiário'),

                        Forms\Components\TextInput::make('registration_number')
                            ->label('Matrícula')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ex: 2025001')
                            ->helperText('Número de matrícula único do estagiário'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Data de Início')
                                    ->helperText('Selecione a Data de Início do Estágio')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('end_date', null);
                                    }),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Data de Término')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->minDate(fn (Forms\Get $get) => $get('start_date'))
                                    ->helperText('A data de término deve ser posterior à data de início'),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Vinculação Institucional')
                    ->description('Informações sobre o vínculo institucional do estágio')
                    ->icon('heroicon-o-building-office')
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_number')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Setor')
                    ->sortable(),

                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->sortable(),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('Curso')
                    ->sortable(),

                Tables\Columns\TextColumn::make('internshipAgency.trade_name')
                    ->label('Agente de Integração')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            RelationManagers\CommitmentTermRelationManager::class,
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