<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternResource\Pages;
use App\Models\Course;
use App\Models\Intern;
use App\Filament\Resources\InternResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternResource extends Resource
{
    protected static ?string $model = Intern::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

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
                            ->description('Dados básicos do estagiário')
                            ->icon('heroicon-o-user')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite o nome completo do estagiário')
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('registration_number')
                                            ->label('Matrícula')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('Ex: 2025001')
                                            ->helperText('Número de matrícula único do estagiário')
                                            ->prefixIcon('heroicon-m-identification'),
                                        Forms\Components\TextInput::make('email')
                                            ->label('E-mail')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('email@exemplo.com')
                                            ->prefixIcon('heroicon-m-envelope'),
                                    ]),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefone')
                                    ->tel()
                                    ->mask('(99) 99999-9999')
                                    ->placeholder('(00) 00000-0000')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->helperText('Número para contato com DDD'),
                            ]),

                        Forms\Components\Section::make('Foto')
                            ->description('Foto de identificação do estagiário')
                            ->icon('heroicon-o-camera')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Foto do Perfil')
                                    ->image()
                                    ->directory('interns')
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->helperText('Faça upload de uma foto de identificação')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make('Vinculação Institucional')
                            ->description('Informações sobre a vinculação do estagiário')
                            ->icon('heroicon-o-building-office-2')
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\Grid::make(2)
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
                                            ->helperText('Selecione o curso do estagiário')
                                            ->prefixIcon('heroicon-m-academic-cap'),

                                        Forms\Components\Select::make('department_id')
                                            ->label('Setor')
                                            ->relationship('department', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Setor onde o estágio será realizado')
                                            ->prefixIcon('heroicon-m-building-office'),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('supervisor_id')
                                            ->label('Supervisor')
                                            ->relationship('supervisor', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Supervisor responsável pelo estagiário')
                                            ->prefixIcon('heroicon-m-user-group'),

                                        Forms\Components\Select::make('internship_agency_id')
                                            ->label('Agente de Integração')
                                            ->relationship('internshipAgency', 'trade_name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Agente de integração responsável')
                                            ->prefixIcon('heroicon-o-building-office'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.acronym')
                    ->label('Setor')
                    ->sortable(),

                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->sortable(),

                Tables\Columns\TextColumn::make('internshipAgency.company_name')
                    ->label('Agente de Integração')
                    ->sortable(),

                Tables\Columns\IconColumn::make('isCurrentlyOnVacation')
                    ->label('Em Férias')
                    ->boolean()
                    ->state(fn ($record) => $record->isCurrentlyOnVacation())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderByVacationStatus($direction)),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('vacation_status')
                    ->label('Status de Férias')
                    ->placeholder('Todos os Estagiários')
                    ->trueLabel('Em Férias')
                    ->falseLabel('Não está em Férias')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('vacations', function (Builder $query) {
                            $today = now()->startOfDay();
                            $query->where('start_date', '<=', $today)
                                ->where('end_date', '>=', $today);
                        }),
                        false: fn (Builder $query) => $query->whereDoesntHave('vacations', function (Builder $query) {
                            $today = now()->startOfDay();
                            $query->where('start_date', '<=', $today)
                                ->where('end_date', '>=', $today);
                        }),
                    ),
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
