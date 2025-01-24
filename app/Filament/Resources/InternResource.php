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
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Informações Pessoais')
                            ->description('Informações básicas do estagiário')
                            ->icon('heroicon-o-user')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Digite o nome completo')
                                            ->prefixIcon('heroicon-m-user')
                                            ->helperText('Nome completo do estagiário'),

                                        Forms\Components\TextInput::make('email')
                                            ->label('E-mail')
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('exemplo@email.com')
                                            ->prefixIcon('heroicon-m-envelope')
                                            ->helperText('E-mail para contato'),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Telefone')
                                            ->tel()
                                            ->maxLength(255)
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
                    ->circular()
                    ->label('Foto')
                    ->defaultImageUrl(function ($record) {
                        $name = collect(explode(' ', $record->name))
                            ->map(fn ($segment) => mb_substr($segment, 0, 1))
                            ->join('');
                        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827';
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-identification'),
                Tables\Columns\TextColumn::make('internships.department.acronym')
                    ->label('Setor')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('internships.course.name')
                    ->label('Curso')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\TextColumn::make('internships.educationalInstitution.trade_name')
                    ->label('Instituição Educacional')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\TextColumn::make('internships.supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('internships.status')
                    ->label('Status')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Ativo',
                        'inactive' => 'Inativo',
                        default => $state,
                    }),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('lg'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Ativo',
                        'inactive' => 'Inativo',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('internships', function ($query) use ($data) {
                                $query->where('status', $data['value']);
                            });
                        }
                    })
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
