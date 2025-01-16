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

class InternResource extends Resource
{
    protected static ?string $model = Intern::class;
    protected static ?string $modelLabel = 'Estagiário';
    protected static ?string $pluralModelLabel = 'Estagiários';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

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
                                        Forms\Components\TextInput::make('email')
                                            ->label('E-mail')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('email@exemplo.com')
                                            ->prefixIcon('heroicon-m-envelope'),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Telefone')
                                            ->tel()
                                            ->mask('(99) 99999-9999')
                                            ->placeholder('(00) 00000-0000')
                                            ->prefixIcon('heroicon-m-phone')
                                            ->helperText('Número para contato com DDD'),
                                    ]),
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=111827';
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('E-mail copiado!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_currently_on_vacation')
                    ->label('Em Férias')
                    ->boolean()
                    ->getStateUsing(fn (Intern $record): bool => $record->isCurrentlyOnVacation())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('vacation_status')
                    ->label('Status de Férias')
                    ->placeholder('Todos os Estagiários')
                    ->trueLabel('Em Férias')
                    ->falseLabel('Não está em Férias')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('internships', function (Builder $query) {
                            $query->whereHas('vacations', function (Builder $query) {
                                $query->whereDate('start_date', '<=', now())
                                    ->whereDate('end_date', '>=', now());
                            });
                        }),
                        false: fn (Builder $query) => $query->whereDoesntHave('internships', function (Builder $query) {
                            $query->whereHas('vacations', function (Builder $query) {
                                $query->whereDate('start_date', '<=', now())
                                    ->whereDate('end_date', '>=', now());
                            });
                        }),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('lg'),
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
            RelationManagers\InternshipsRelationManager::class,
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
}
