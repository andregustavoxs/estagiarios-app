<?php

namespace App\Filament\Resources\InternResource\RelationManagers;

use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InternshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'internships';

    protected static ?string $title = 'Estágios';

    protected static ?string $modelLabel = 'Estágios';

    protected static ?string $pluralModelLabel = 'Estagiários';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informações do Estágio')
                        ->description('Informações básicas do estágio')
                        ->icon('heroicon-o-user')
                        ->columns(2)
                        ->schema([
                            Forms\Components\Select::make('intern_id')
                                ->relationship('intern', 'name')
                                ->label('Estagiário')
                                ->required()
                                ->disabled()
                                ->searchable()
                                ->preload()
                                ->columnSpan(1)
                                ->helperText('Selecione o estagiário'),

                            Forms\Components\TextInput::make('registration_number')
                                ->label('Matrícula')
                                ->required()
                                ->disabled()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->columnSpan(1)
                                ->placeholder('Ex: 2025001')
                                ->helperText('Número de matrícula único do estagiário'),

                            Forms\Components\DatePicker::make('start_date')
                                ->label('Data de Início')
                                ->helperText('Selecione a Data de Início do Estágio')
                                ->required()
                                ->disabled()
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->closeOnDateSelection()
                                ->columnSpan(1)
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    $set('end_date', null);
                                }),

                            Forms\Components\DatePicker::make('end_date')
                                ->label('Data de Término')
                                ->required()
                                ->disabled()
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->closeOnDateSelection()
                                ->columnSpan(1)
                                ->minDate(fn(Forms\Get $get) => $get('start_date'))
                                ->helperText('A data de término deve ser posterior à data de início'),
                        ]),

                    Forms\Components\Wizard\Step::make('Vinculação Institucional')
                        ->description('Vínculo institucional do estágio')
                        ->icon('heroicon-o-building-office')
                        ->columns(2)
                        ->schema([
                            Forms\Components\Select::make('course_id')
                                ->label('Curso')
                                ->relationship('course', 'name')
                                ->required()
                                ->disabled()
                                ->live()
                                ->searchable()
                                ->preload()
                                ->columnSpan(1)
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
                                        return [$course->id => $course->name.$suffix];
                                    });
                                })
                                ->helperText('Selecione o curso do estagiário'),

                            Forms\Components\Select::make('department_id')
                                ->label('Setor')
                                ->relationship('department', 'name')
                                ->required()
                                ->disabled()
                                ->searchable()
                                ->preload()
                                ->columnSpan(1)
                                ->helperText('Setor onde o estágio será realizado'),

                            Forms\Components\Select::make('supervisor_id')
                                ->label('Supervisor')
                                ->relationship('supervisor', 'name')
                                ->required()
                                ->searchable()
                                ->disabled()
                                ->preload()
                                ->columnSpan(1)
                                ->helperText('Supervisor responsável pelo estagiário'),

                            Forms\Components\Select::make('internship_agency_id')
                                ->label('Agente de Integração')
                                ->relationship('internshipAgency', 'trade_name')
                                ->required()
                                ->searchable()
                                ->disabled()
                                ->preload()
                                ->columnSpan(1)
                                ->helperText('Agente de integração responsável'),
                        ]),
                ])
                    ->columnSpan('full')
                    ->skippable()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(fn($livewire) => 'Estágios de '.$livewire->getOwnerRecord()->name)
            ->description('Histórico de estágios deste estagiário')
            ->columns([
                Tables\Columns\TextColumn::make('department.acronym')
                    ->label('Setor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Curso')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\TextColumn::make('internshipAgency.trade_name')
                    ->label('Agente de Integração')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-library'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar')
                    ->modalHeading('Visualizar Estágio'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
