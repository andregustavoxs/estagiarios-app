<?php

namespace App\Filament\Resources\EducationalInstitutionResource\RelationManagers;

use App\Models\Intern;
use App\Models\Internship;
use App\Models\EducationalInstitution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InternsRelationManager extends RelationManager
{
    protected static string $relationship = 'internships';

    protected static ?string $recordTitleAttribute = 'intern.name';

    protected static ?string $title = 'Estagiários';

    protected static ?string $modelLabel = 'Estagiário';

    protected static ?string $pluralModelLabel = 'Estagiários';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informações Pessoais')
                        ->description('Dados pessoais do estagiário')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('intern.name')
                                    ->label('Nome')
                                    ->disabled()
                                    ->maxLength(255)
                                    ->placeholder('Digite o nome completo')
                                    ->helperText('Nome completo do estagiário')
                                    ->prefixIcon('heroicon-o-user')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->intern) {
                                            $component->state($record->intern->name);
                                        }
                                    }),

                                Forms\Components\TextInput::make('registration_number')
                                    ->label('Matrícula')
                                    ->disabled()
                                    ->maxLength(255)
                                    ->placeholder('Digite o número da matrícula')
                                    ->helperText('Número de matrícula do estagiário')
                                    ->prefixIcon('heroicon-o-identification'),
                            ]),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('intern.email')
                                    ->label('E-mail')
                                    ->email()
                                    ->disabled()
                                    ->maxLength(255)
                                    ->placeholder('Digite o e-mail')
                                    ->helperText('E-mail do estagiário')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->intern) {
                                            $component->state($record->intern->email);
                                        }
                                    }),

                                Forms\Components\TextInput::make('intern.phone')
                                    ->label('Telefone')
                                    ->tel()
                                    ->disabled()
                                    ->maxLength(20)
                                    ->mask('(99) 99999-9999')
                                    ->placeholder('(00) 00000-0000')
                                    ->helperText('Telefone do estagiário')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->intern) {
                                            $component->state($record->intern->phone);
                                        }
                                    }),
                            ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Informações do Estágio')
                        ->description('Dados relacionados ao estágio')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('department_id')
                                        ->relationship('department', 'name')
                                        ->label('Setor')
                                        ->required()
                                        ->placeholder('Selecione o setor')
                                        ->helperText('Setor onde o estágio será realizado')
                                        ->prefixIcon('heroicon-o-building-office'),

                                    Forms\Components\Select::make('course_id')
                                        ->relationship('course', 'name')
                                        ->label('Curso')
                                        ->required()
                                        ->placeholder('Selecione o curso')
                                        ->helperText('Curso do estagiário')
                                        ->prefixIcon('heroicon-o-academic-cap'),

                                    Forms\Components\Select::make('supervisor_id')
                                        ->relationship('supervisor', 'name')
                                        ->label('Supervisor')
                                        ->required()
                                        ->placeholder('Selecione o supervisor')
                                        ->helperText('Supervisor responsável')
                                        ->columnSpan(1)
                                        ->prefixIcon('heroicon-o-user'),

                                    Forms\Components\Select::make('educational_institution_id')
                                        ->relationship('educationalInstitution', 'trade_name')
                                        ->label('Instituição de Ensino')
                                        ->required()
                                        ->placeholder('Selecione a instituição de ensino do estagiário')
                                        ->helperText('Instituição responsável pelo estagiário')
                                        ->prefixIcon('heroicon-o-building-library'),

                                    Forms\Components\Select::make('education_level')
                                        ->label('Nível de Formação')
                                        ->options([
                                            'postgraduate' => 'Pós-Graduação',
                                            'higher_education' => 'Ensino Superior',
                                            'technical' => 'Ensino Técnico',
                                        ])
                                        ->required()
                                        ->disabled()
                                        ->placeholder('Selecione o nível de formação')
                                        ->helperText('Nível de formação do estagiário')
                                        ->prefixIcon('heroicon-o-academic-cap'),
                                ]),
                        ]),
                ])->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('intern.internships'))
            ->recordTitleAttribute('intern.name')
            ->heading(fn($livewire) => 'Estagiários do curso de '.$livewire->getOwnerRecord()->name)
            ->description('Lista de estagiários matriculados neste curso')
            ->columns([
                Tables\Columns\TextColumn::make('intern.name')
                ->label('Nome')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->icon('heroicon-m-user'),
            Tables\Columns\TextColumn::make('intern.internships.department.acronym')
                ->label('Setor')
                ->searchable()
                ->badge(),
            Tables\Columns\TextColumn::make('intern.internships.course.name')
                ->label('Curso')
                ->searchable()
                ->icon('heroicon-m-academic-cap'),
            Tables\Columns\TextColumn::make('intern.internships.educationalInstitution.trade_name')
                ->label('Instituição de Ensino')
                ->searchable()
                ->icon('heroicon-m-building-library'),
            Tables\Columns\TextColumn::make('intern.internships.education_level')
                ->label('Nível de Formação')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'postgraduate' => 'Pós-Graduação',
                    'higher_education' => 'Ensino Superior',
                    'technical' => 'Ensino Técnico',
                    default => $state,
                })
                ->sortable(),
            Tables\Columns\TextColumn::make('intern.internships.supervisor.name')
                ->label('Supervisor')
                ->searchable()
                ->icon('heroicon-m-user'),
            ])
            ->defaultSort('intern.name', 'asc')
            ->striped()
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-m-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
                ]),
            ]);
    }
}
