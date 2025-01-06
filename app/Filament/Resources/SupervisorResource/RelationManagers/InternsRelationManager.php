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

    protected static ?string $modelLabel = 'estagiário';

    protected static ?string $pluralModelLabel = 'estagiários';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Pessoais')
                    ->description('Dados pessoais do estagiário')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o nome completo')
                            ->helperText('Nome completo do estagiário')
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('registration_number')
                            ->label('Matrícula')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o número da matrícula')
                            ->helperText('Número de matrícula do estagiário')
                            ->prefixIcon('heroicon-o-identification')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'Esta matrícula já está em uso.',
                            ]),

                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o e-mail')
                            ->helperText('E-mail institucional ou pessoal')
                            ->prefixIcon('heroicon-o-envelope'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->required()
                            ->maxLength(20)
                            ->mask('(99) 99999-9999')
                            ->placeholder('(00) 00000-0000')
                            ->helperText('Número de telefone celular')
                            ->prefixIcon('heroicon-o-phone'),
                    ]),

                Forms\Components\Section::make('Informações do Estágio')
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

                                Forms\Components\Select::make('internship_agency_id')
                                    ->relationship('internshipAgency', 'company_name')
                                    ->label('Agente de Integração')
                                    ->required()
                                    ->placeholder('Selecione o agente')
                                    ->helperText('Agente de integração responsável')
                                    ->prefixIcon('heroicon-o-building-library'),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.acronym')
                    ->label('Setor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internshipAgency.trade_name')
                    ->label('Agente de Integração')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Removed CreateAction
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
                ]),
            ]);
    }
}
