<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternResource\Pages;
use App\Filament\Resources\InternResource\RelationManagers;
use App\Models\Intern;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternResource extends Resource
{
    protected static ?string $model = Intern::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Estagiário';

    protected static ?string $pluralModelLabel = 'Estagiários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 3,
                ])
                    ->schema([
                        Forms\Components\Section::make('Informações Pessoais')
                            ->description('Informações básicas do estagiário')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('registration_number')
                                            ->label('Matrícula')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email')
                                            ->label('E-mail')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                    ]),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefone')
                                    ->tel()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Section::make('Foto do Estagiário')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->image()
                                    ->directory('interns')
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ]),
                            ]),

                        Forms\Components\Section::make('Informações Organizacionais')
                            ->description('Detalhes do departamento e curso')
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('supervisor_id')
                                            ->label('Supervisor')
                                            ->relationship('supervisor', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('department_id')
                                            ->label('Setor')
                                            ->relationship('department', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('course_id')
                                            ->label('Curso')
                                            ->relationship('course', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Curso')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.acronym')
                    ->label('Setor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->sortable()
                    ->searchable(),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterns::route('/'),
            'create' => Pages\CreateIntern::route('/create'),
            'edit' => Pages\EditIntern::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Estagiários';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Estagiários';
    }

    public static function getModelLabel(): string
    {
        return 'Estagiário';
    }
}
