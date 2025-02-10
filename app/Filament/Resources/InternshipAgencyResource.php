<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternshipAgencyResource\Pages;
use App\Filament\Resources\InternshipAgencyResource\RelationManagers;
use App\Models\InternshipAgency;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informações da Agência')
                        ->description('Dados cadastrais da agência de estágio')
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
                                        ->columnSpanFull(),

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
                        ]),

                    Step::make('Endereço')
                        ->description('Informações de localização do Agente de Integração')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\TextInput::make('postal_code')
                                ->label('CEP')
                                ->mask('99999-999')
                                ->placeholder('00000-000')
                                ->prefixIcon('heroicon-o-map')
                                ->helperText('Digite o CEP para autocompletar o endereço')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                    if (!$state) return;

                                    $cep = preg_replace('/[^0-9]/', '', $state);

                                    try {
                                        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

                                        if ($response->successful() && $response->json() && !isset($response->json()['erro'])) {
                                            $data = $response->json();

                                            $set('address', $data['logradouro'] ?? '');
                                            $set('neighborhood', $data['bairro'] ?? '');
                                            $set('city', $data['localidade'] ?? '');
                                            $set('uf', $data['uf'] ?? '');

                                            Notification::make()
                                                ->success()
                                                ->title('Endereço encontrado')
                                                ->body('Os campos foram preenchidos automaticamente.')
                                                ->send();
                                        } else {
                                            Notification::make()
                                                ->danger()
                                                ->title('CEP não encontrado')
                                                ->body('Verifique o CEP digitado.')
                                                ->send();
                                        }
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Erro ao buscar CEP')
                                            ->body('Não foi possível consultar o CEP. Tente novamente.')
                                            ->send();
                                    }
                                }),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('address')
                                        ->label('Endereço')
                                        ->prefixIcon('heroicon-o-home')
                                        ->placeholder('Rua, Avenida, etc')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('number')
                                        ->label('Número')
                                        ->prefixIcon('heroicon-o-hashtag')
                                        ->placeholder('123')
                                        ->maxLength(255),
                                ]),
                            Forms\Components\TextInput::make('complement')
                                ->label('Complemento')
                                ->prefixIcon('heroicon-o-information-circle')
                                ->placeholder('Apto, Sala, etc')
                                ->maxLength(255),
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('neighborhood')
                                        ->label('Bairro')
                                        ->prefixIcon('heroicon-o-building-office')
                                        ->placeholder('Nome do bairro')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('city')
                                        ->label('Cidade')
                                        ->prefixIcon('heroicon-o-building-office-2')
                                        ->placeholder('Nome da cidade')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('uf')
                                        ->label('UF')
                                        ->prefixIcon('heroicon-o-flag')
                                        ->placeholder('SP')
                                        ->maxLength(2)
                                        ->formatStateUsing(
                                            fn(?string $state): string => $state ? strtoupper($state) : ''
                                        )
                                        ->dehydrateStateUsing(
                                            fn(?string $state): string => $state ? strtoupper($state) : ''
                                        ),
                                ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Informações do Convênio')
                        ->description('Dados do convênio com a agência de estágio')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\TextInput::make('agreement_number')
                                ->label('Número do Convênio')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->placeholder('Digite o número do convênio')
                                ->helperText('Número único do convênio')
                                ->prefixIcon('heroicon-o-hashtag'),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('agreement_validity_start')
                                        ->label('Início da Vigência')
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->prefixIcon('heroicon-o-calendar')
                                        ->default(now())
                                        ->helperText('Data de início do convênio'),

                                    Forms\Components\DatePicker::make('agreement_validity_end')
                                        ->label('Fim da Vigência')
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->prefixIcon('heroicon-o-calendar')
                                        ->default(now()->addYears(2))
                                        ->helperText('Data de término do convênio')
                                        ->afterOrEqual('agreement_validity_start'),
                                ]),
                        ]),
                ])->columnSpanFull(),
            ]);
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
                Tables\Columns\TextColumn::make('agreement_validity_end')
                    ->label('Fim do Convênio')
                    ->date('d/m/Y')
                    ->sortable(),
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
            //
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
