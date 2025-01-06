<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternshipAgencyResource\Pages;
use App\Filament\Resources\InternshipAgencyResource\RelationManagers;
use App\Models\InternshipAgency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class InternshipAgencyResource extends Resource
{
    protected static ?string $model = InternshipAgency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $modelLabel = 'Agente de Integração';
    
    protected static ?string $pluralModelLabel = 'Agentes de Integrações';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Empresa')
                    ->description('Dados cadastrais do agente de integração')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Razão Social')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite a razão social da empresa')
                                    ->helperText('Nome oficial registrado da empresa')
                                    ->prefixIcon('heroicon-o-building-office')
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
                                    ->helperText('Nome comercial ou marca da empresa')
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
                                    ->helperText('CNPJ da empresa (apenas números)')
                                    ->prefixIcon('heroicon-o-identification')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este CNPJ já está cadastrado.',
                                    ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Informações de Contato')
                    ->description('Dados para contato com o agente de integração')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefone')
                                    ->required()
                                    ->maxLength(20)
                                    ->mask('(99) 99999-9999')
                                    ->placeholder('(00) 00000-0000')
                                    ->helperText('Telefone principal para contato')
                                    ->prefixIcon('heroicon-o-phone'),

                                Forms\Components\TextInput::make('contact_person')
                                    ->label('Pessoa de Contato')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Nome da pessoa responsável')
                                    ->helperText('Nome do responsável pelo contato')
                                    ->prefixIcon('heroicon-o-user'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Razão Social')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trade_name')
                    ->label('Nome Fantasia')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Pessoa de Contato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($action, InternshipAgency $record) {
                        if ($record->interns()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Ação bloqueada')
                                ->body('Não é possível excluir este agente de integração pois existem estagiários vinculados a ele.')
                                ->send();
                            
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($action, Collection $records) {
                            foreach ($records as $record) {
                                if ($record->interns()->count() > 0) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Ação bloqueada')
                                        ->body('Não é possível excluir agentes de integração que possuem estagiários vinculados.')
                                        ->send();
                                    
                                    $action->cancel();
                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InternsRelationManager::class,
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
