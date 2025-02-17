<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationTemplateResource\Pages;
use App\Models\EvaluationTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class EvaluationTemplateResource extends Resource
{
    protected static ?string $model = EvaluationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Modelos';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Modelo de Avaliação';

    protected static ?string $pluralModelLabel = 'Modelos de Avaliação';

    protected static ?string $modelLabel = 'Modelo de Avaliação';

    protected static ?string $slug = 'modelo-de-avaliacao';

    protected static ?string $pluralLabel = 'Modelos de Avaliação';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Modelo')
                    ->description('Preencha as informações do modelo de avaliação')
                    ->icon('heroicon-o-document-text')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Formulário de Avaliação 2025')
                            ->helperText('Digite um nome descritivo para identificar este modelo')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('Modelo de Avaliação PDF')
                            ->required()
                            ->directory('evaluation-templates')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->downloadable()
                            ->helperText('Faça upload do arquivo PDF do modelo de avaliação (máx. 5MB)')
                            ->columnSpanFull()
                            ->preserveFilenames()
                            ->previewable(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Descrição')
                            ->maxLength(65535)
                            ->placeholder('Descreva o propósito deste modelo de avaliação...')
                            ->helperText('Uma breve descrição ajudará a identificar o propósito deste modelo')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Criado em')
                    ->date('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Baixar')
                    ->color('success')
                    ->action(function ($record) {
                        return response()->download(
                            $record->getStorageFilePath(),
                            'Formulário de Avaliação.pdf'
                        );
                    }),
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
            'index' => Pages\ListEvaluationTemplates::route('/'),
            'create' => Pages\CreateEvaluationTemplate::route('/create'),
            'edit' => Pages\EditEvaluationTemplate::route('/{record}/edit'),
        ];
    }
}
