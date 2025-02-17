<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternshipAgencyResource\Pages;
use App\Filament\Resources\InternshipAgencyResource\RelationManagers;
use App\Models\InternshipAgency;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;

class InternshipAgencyResource extends Resource
{
    protected static ?string $model = InternshipAgency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Agente de Integração';

    protected static ?string $pluralModelLabel = 'Agentes de Integração';

    protected static ?string $modelLabel = 'Agente de Integração';

    protected static ?string $slug = 'agente-de-integracao';

    protected static ?string $pluralLabel = 'Agentes de Integração';

    protected static ?string $navigationGroup = 'Estágios';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informações da Agência')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Razão Social')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite a razão social da agência')
                                    ->helperText('Nome oficial registrado da agência')
                                    ->prefixIcon('heroicon-o-building-library')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Esta razão social já está em uso.',
                                    ])
                                    ->columnSpan('full'),

                                Forms\Components\TextInput::make('trade_name')
                                    ->label('Nome Fantasia')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite o nome fantasia')
                                    ->helperText('Nome comercial ou marca da agência')
                                    ->prefixIcon('heroicon-o-building-storefront')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este nome fantasia já está em uso.',
                                    ]),

                                Forms\Components\TextInput::make('cnpj')
                                    ->label('CNPJ')
                                    ->required()
                                    ->maxLength(18)
                                    ->mask('99.999.999/9999-99')
                                    ->placeholder('00.000.000/0000-00')
                                    ->helperText('CNPJ da agência (apenas números)')
                                    ->prefixIcon('heroicon-o-identification')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este CNPJ já está cadastrado.',
                                    ]),
                            ]),
                    ])->columnSpan('full'),

                Forms\Components\Wizard\Step::make('Endereço')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Forms\Components\TextInput::make('cep')
                            ->label('CEP')
                            ->required()
                            ->mask('99999-999')
                            ->placeholder('00000-000')
                            ->prefixIcon('heroicon-o-map')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                if (!$state) {
                                    return;
                                }

                                try {
                                    $response = Http::get("https://viacep.com.br/ws/{$state}/json/");
                                    $address = $response->json();

                                    if (!$response->successful() || isset($address['erro'])) {
                                        return;
                                    }

                                    $set('street', $address['logradouro'] ?? '');
                                    $set('neighborhood', $address['bairro'] ?? '');
                                    $set('city', $address['localidade'] ?? '');
                                    $set('uf', $address['uf'] ?? '');
                                } catch (\Exception $e) {
                                    // Log or handle the error as needed
                                }
                            }),

                        Forms\Components\TextInput::make('street')
                            ->label('Logradouro')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o logradouro')
                            ->prefixIcon('heroicon-o-map-pin'),

                        Forms\Components\TextInput::make('neighborhood')
                            ->label('Bairro')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o bairro')
                            ->prefixIcon('heroicon-o-map-pin'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->label('Cidade')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite a cidade')
                                    ->prefixIcon('heroicon-o-building-office-2'),

                                Forms\Components\TextInput::make('uf')
                                    ->label('UF')
                                    ->required()
                                    ->length(2)
                                    ->placeholder('UF')
                                    ->prefixIcon('heroicon-o-building-office-2'),
                            ]),
                    ])->columnSpan('full'),
                ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Razão Social')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-building-library'),
                Tables\Columns\TextColumn::make('trade_name')
                    ->label('Nome Fantasia')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-storefront'),
            ])
            ->filters([
                //
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
            RelationManagers\AgreementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternshipAgencies::route('/'),
            'create' => Pages\CreateInternshipAgency::route('/create'),
            'edit' => Pages\EditInternshipAgency::route('/{record}/edit'),
        ];
    }
}
