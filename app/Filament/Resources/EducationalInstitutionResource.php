<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EducationalInstitutionResource\Pages;
use App\Filament\Resources\EducationalInstitutionResource\RelationManagers;
use App\Models\EducationalInstitution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class EducationalInstitutionResource extends Resource
{
    protected static ?string $model = EducationalInstitution::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $modelLabel = 'Instituição de Ensino';

    protected static ?string $pluralModelLabel = 'Instituições de Ensino';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Instituição de Ensino')
                    ->description('Dados cadastrais da instituição de ensino')
                    ->icon('heroicon-o-academic-cap')    
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Razão Social')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite a razão social da instituição de ensino')
                                    ->helperText('Nome oficial registrado da instituição de ensino')
                                    ->prefixIcon('heroicon-o-academic-cap')
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
                                    ->helperText('Nome comercial ou marca da instituição de ensino')
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
                                    ->helperText('CNPJ da instituição de ensino (apenas números)')        
                                    ->prefixIcon('heroicon-o-identification')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este CNPJ já está cadastrado.',
                                    ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Informações de Contato')
                    ->description('Dados para contato com a instituição de ensino')
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
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-building-library'),
                Tables\Columns\TextColumn::make('trade_name')
                    ->label('Nome Fantasia')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-storefront'),
                Tables\Columns\TextColumn::make('interns_count')
                    ->label('Qtd. Estagiários')
                    ->counts('interns')
                    ->sortable()
                    ->icon('heroicon-m-user-group')
                    ->alignEnd(),
            ])
            ->defaultSort('company_name', 'asc')
            ->striped()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($action, EducationalInstitution $record) {
                        if ($record->interns()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Ação bloqueada')
                                ->body('Não é possível excluir esta instituição de ensino pois existem estagiários vinculados a ela.')
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
                                        ->body('Não é possível excluir instituções de ensino que possuem estagiários vinculados.')
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
            'index' => Pages\ListEducationalInstitutions::route('/'),
            'create' => Pages\CreateEducationalInstitution::route('/create'),
            'edit' => Pages\EditEducationalInstitution::route('/{record}/edit'),
        ];
    }
}
