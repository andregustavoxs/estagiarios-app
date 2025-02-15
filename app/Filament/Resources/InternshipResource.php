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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternshipResource extends Resource
{
    protected static ?string $model = Internship::class;

    protected static ?string $modelLabel = 'Estágio';

    protected static ?string $pluralModelLabel = 'Estágios';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $slug = 'estagios';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informações do Estágio')
                        ->icon('heroicon-o-user')
                        ->description('Informações Básicas do Estágio')
                        ->schema([
                            Forms\Components\Select::make('intern_id')
                                ->relationship('intern', 'name')
                                ->label('Estagiário')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->helperText('Selecione o estagiário'),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('registration_number')
                                        ->label('Matrícula')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->maxLength(255)
                                        ->placeholder('Ex: 2025001')
                                        ->helperText('Número de matrícula único do estagiário'),

                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'active' => 'Ativo',
                                            'inactive' => 'Inativo',
                                        ])
                                        ->required()
                                        ->native(false)
                                        ->helperText('Status do estágio')
                                        ->prefixIcon('heroicon-o-check-circle'),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('start_date')
                                        ->label('Data de Início')
                                        ->required()
                                        ->displayFormat('d/m/Y')
                                        ->format('Y-m-d')
                                        ->closeOnDateSelection()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                                            if ($state) {
                                                $endDate = \Carbon\Carbon::parse($state)->addMonths(6);
                                                $set('end_date', $endDate->format('Y-m-d'));
                                            }
                                        }),

                                    Forms\Components\DatePicker::make('end_date')
                                        ->label('Data de Término')
                                        ->required()
                                        ->displayFormat('d/m/Y')
                                        ->format('Y-m-d')
                                        ->closeOnDateSelection()
                                        ->minDate(fn(Forms\Get $get) => $get('start_date'))
                                        ->helperText('A data de término deve ser posterior à data de início'),
                                ]),
                        ])->columns(2),


                    Forms\Components\Wizard\Step::make('Vinculação Institucional')
                        ->description('Vínculo institucional do estágio')
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
                                        return [$course->id => $course->name.$suffix];
                                    });
                                })
                                ->helperText('Selecione o curso do estagiário'),

                            Forms\Components\Select::make('department_id')
                                ->label('Setor')
                                ->relationship('department', 'acronym')
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

                            Forms\Components\Select::make('educational_institution_id')
                                ->label('Instituição Educacional')
                                ->relationship('educationalInstitution', 'trade_name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->helperText('Instituição educacional responsável'),

                            Forms\Components\Select::make('education_level')
                                ->label('Nível de Formação')
                                ->options([
                                    'postgraduate' => 'Pós-Graduação',
                                    'higher_education' => 'Ensino Superior',
                                    'technical' => 'Ensino Técnico',
                                ])
                                ->required(),
                        ])->columns(2)
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('intern.internships.registration_number')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-identification'),
                Tables\Columns\TextColumn::make('intern.name')
                    ->label('Estagiário')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user'),
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
                    ->weight('bold')
                    ->icon('heroicon-m-building-library'),
            ])
            ->defaultSort('intern.name', 'asc')
            ->striped()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Estágio arquivado')
                            ->body('O estágio foi movido para a lixeira.')
                    ),
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
            RelationManagers\CommitmentTermRelationManager::class,
            RelationManagers\EvaluationsRelationManager::class,
            RelationManagers\AddendumsRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed();
    }
}
