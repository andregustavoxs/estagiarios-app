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
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('cnpj')
                    ->required()
                    ->maxLength(18)
                    ->label('CNPJ'),
                Forms\Components\TextInput::make('company_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome'),
                Forms\Components\TextInput::make('trade_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome Fantasia'),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(20)
                    ->label('Telefone'),
                Forms\Components\TextInput::make('contact_person')
                    ->required()
                    ->maxLength(255)
                    ->label('Pessoa de Contato'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trade_name')
                    ->label('Nome Fantasia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
