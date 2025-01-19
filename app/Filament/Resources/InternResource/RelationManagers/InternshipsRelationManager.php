<?php

namespace App\Filament\Resources\InternResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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
                Forms\Components\TextInput::make('registration_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Número de Matrícula'),

                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Departamento'),

                Forms\Components\Select::make('supervisor_id')
                    ->relationship('supervisor', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Supervisor'),

                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Curso'),

                Forms\Components\Select::make('internship_agency_id')
                    ->relationship('internshipAgency', 'trade_name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Agente de Integração'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('registration_number')
            ->columns([
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('Curso')
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
