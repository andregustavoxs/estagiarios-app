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
                        ->description('Dados básicos do estágio')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('registration_number')
                                    ->label('Matrícula')
                                    ->required()
                                    ->disabled()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Ex: 2025001')
                                    ->helperText('Número de matrícula único do estagiário')
                                    ->prefixIcon('heroicon-o-identification'),

                                Forms\Components\Select::make('intern_id')
                                    ->relationship('intern', 'name')
                                    ->label('Estagiário')
                                    ->required()
                                    ->disabled()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Selecione o estagiário')
                                    ->prefixIcon('heroicon-o-user'),
                            ]),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Data de Início')
                                    ->helperText('Selecione a Data de Início do Estágio')
                                    ->required()
                                    ->disabled()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->prefixIcon('heroicon-o-calendar')
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
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->minDate(fn(Forms\Get $get) => $get('start_date'))
                                    ->helperText('A data de término deve ser posterior à data de início'),
                            ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Vinculação Institucional')
                        ->description('Vínculo institucional do estágio')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('department_id')
                                    ->label('Setor')
                                    ->relationship('department', 'name')
                                    ->required()
                                    ->disabled()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Setor onde o estágio será realizado')
                                    ->prefixIcon('heroicon-o-building-office'),

                                Forms\Components\Select::make('course_id')
                                    ->label('Curso')
                                    ->relationship('course', 'name')
                                    ->required()
                                    ->disabled()
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Selecione o curso do estagiário')
                                    ->prefixIcon('heroicon-o-academic-cap')
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
                                    }),

                                Forms\Components\Select::make('supervisor_id')
                                    ->label('Supervisor')
                                    ->relationship('supervisor', 'name')
                                    ->required()
                                    ->searchable()
                                    ->disabled()
                                    ->preload()
                                    ->helperText('Supervisor responsável pelo estagiário')
                                    ->prefixIcon('heroicon-o-user'),

                                Forms\Components\Select::make('educational_institution_id')
                                    ->label('Instituição de Ensino')
                                    ->relationship('educationalInstitution', 'trade_name')
                                    ->required()
                                    ->searchable()
                                    ->disabled()
                                    ->preload()
                                    ->helperText('Instituição responsável pelo estagiário')
                                    ->prefixIcon('heroicon-o-building-library'),
                            ]),
                        ]),
                ])->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(fn($livewire) => 'Estágios de '.$livewire->getOwnerRecord()->name)
            ->description('Histórico de estágios deste estagiário')
            ->columns([
                Tables\Columns\TextColumn::make('intern.internships.registration_number')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\TextColumn::make('department.acronym')
                    ->label('Setor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Curso')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\TextColumn::make('intern.internships.educationalInstitution.trade_name')
                    ->label('Instituição de Ensino')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-library'),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user'),
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
