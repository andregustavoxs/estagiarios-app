<?php

namespace App\Filament\Resources\InternshipAgencyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;

class AgreementsRelationManager extends RelationManager
{
    protected static string $relationship = 'agreements';

    protected static ?string $title = 'Convênios';

    protected static ?string $modelLabel = 'convênio';

    protected static ?string $pluralModelLabel = 'convênios';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Convênio')
                    ->description('Dados do convênio com a agência de estágio')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('agreement_number')
                            ->label('Número do Convênio')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Digite o número do convênio')
                            ->prefixIcon('heroicon-o-document-text'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('agreement_validity_start')
                                    ->label('Início da Vigência')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->helperText('Data de início do convênio')
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $endDate = $get('agreement_validity_end');
                                        if ($endDate && $get('agreement_validity_start') > $endDate) {
                                            $set('agreement_validity_end', $get('agreement_validity_start'));
                                        }
                                    })
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $endDate = $get('agreement_validity_end');
                                                if (!$value || !$endDate) return;

                                                $existingOverlap = $this->getOwnerRecord()->agreements()
                                                    ->where(function ($query) use ($value, $endDate) {
                                                        $query->where(function ($q) use ($value, $endDate) {
                                                            $q->where('agreement_validity_start', '<=', $endDate)
                                                              ->where('agreement_validity_end', '>=', $value);
                                                        });
                                                    })
                                                    ->when($get('id'), function ($query, $id) {
                                                        $query->where('id', '!=', $id);
                                                    })
                                                    ->exists();

                                                if ($existingOverlap) {
                                                    $fail('Já existe um convênio ativo neste período.');
                                                }
                                            };
                                        }
                                    ]),

                                Forms\Components\DatePicker::make('agreement_validity_end')
                                    ->label('Fim da Vigência')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->default(now()->addYears(2))
                                    ->helperText('Data de término do convênio')
                                    ->afterOrEqual('agreement_validity_start')
                                    ->live()
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $startDate = $get('agreement_validity_start');
                                                if (!$value || !$startDate) return;

                                                $existingOverlap = $this->getOwnerRecord()->agreements()
                                                    ->where(function ($query) use ($value, $startDate) {
                                                        $query->where(function ($q) use ($value, $startDate) {
                                                            $q->where('agreement_validity_start', '<=', $value)
                                                              ->where('agreement_validity_end', '>=', $startDate);
                                                        });
                                                    })
                                                    ->when($get('id'), function ($query, $id) {
                                                        $query->where('id', '!=', $id);
                                                    })
                                                    ->exists();

                                                if ($existingOverlap) {
                                                    $fail('Já existe um convênio ativo neste período.');
                                                }
                                            };
                                        }
                                    ]),
                            ]),
                    ])->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('agreement_number')
            ->columns([
                Tables\Columns\TextColumn::make('agreement_number')
                    ->label('Número do Convênio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agreement_validity_start')
                    ->label('Início da Vigência')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('agreement_validity_end')
                    ->label('Fim da Vigência')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Model $record): string =>
                        $record->agreement_validity_end < now() ? 'danger' : 'success'
                    ),
            ])
            ->defaultSort('agreement_validity_start', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Convênio'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
