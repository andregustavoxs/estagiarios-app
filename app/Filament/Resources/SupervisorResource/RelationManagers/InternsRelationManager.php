<?php

namespace App\Filament\Resources\SupervisorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternsRelationManager extends RelationManager
{
    protected static string $relationship = 'interns';

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
                                        ->placeholder('Selecione a instituiçãeo de ensino do estagiário')
                                        ->helperText('Instituição de ensino responsável pelo estagiário')
                                        ->prefixIcon('heroicon-o-building-library'),
                                ]),
                        ]),
                ])->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('intern'))
            ->recordTitleAttribute('intern.name')
            ->heading(fn ($livewire) => 'Estagiários supervisionados por ' . $livewire->getOwnerRecord()->name)
            ->description('Lista de estagiários sob supervisão')
            ->columns([
                Tables\Columns\TextColumn::make('intern.name')
                ->label('Nome')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->icon('heroicon-m-identification'),
            Tables\Columns\TextColumn::make('intern.internships.department.acronym')
                ->label('Setor')
                ->searchable()
                ->badge(),
            Tables\Columns\TextColumn::make('intern.internships.course.name')
                ->label('Curso')
                ->searchable()
                ->icon('heroicon-m-academic-cap'),
            Tables\Columns\TextColumn::make('intern.internships.educationalInstitution.trade_name')
                ->label('Instituição Educacional')
                ->searchable()
                ->icon('heroicon-m-building-library'),
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
                    ->label('Visualizar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
                ]),
            ]);
    }
}
