<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ExtensionsRelationManager extends RelationManager
{
    protected static string $relationship = 'extensions';

    protected static ?string $title = 'Ramais';

    protected static ?string $modelLabel = 'ramal';

    protected static ?string $pluralModelLabel = 'ramais';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Ramal')
                    ->description('Dados do ramal do setor')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\TextInput::make('extension')
                            ->label('Ramal')
                            ->required()
                            ->numeric()
                            ->length(4)
                            ->maxLength(4)
                            ->mask('9999')
                            ->placeholder('Ex: 1234')
                            ->helperText('Ramal do setor (4 dígitos)')
                            ->prefixIcon('heroicon-o-phone')
                            ->unique(
                                table: 'extensions',
                                column: 'extension',
                                ignoreRecord: true,
                            )
                            ->validationMessages([
                                'unique' => 'Este ramal já está em uso.',
                                'numeric' => 'O ramal deve conter apenas números.',
                                'length' => 'O ramal deve ter exatamente 4 dígitos.',
                            ]),
                    ])->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('extension')
            ->columns([
                Tables\Columns\TextColumn::make('extension')
                    ->label('Ramal')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data de Cadastro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Ramal'),
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
