<?php

namespace App\Filament\Resources\InternshipResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class EvaluationsRelationManager extends RelationManager
{
    protected static string $relationship = 'evaluations';
    protected static ?string $recordTitleAttribute = 'evaluation_number';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('evaluation_number')
                    ->label('Período de Avaliação')
                    ->options([
                        '1st_evaluation' => '1° Período',
                        '2st_evaluation' => '2° Período',
                        '3st_evaluation' => '3° Período',
                        '4st_evaluation' => '4° Período',
                    ])
                    ->required()
                    ->native(false)
                    ->prefixIcon('heroicon-o-check-circle'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'finished' => 'Finalizado',
                        'pending' => 'Pendente',
                    ])
                    ->required()
                    ->native(false)
                    ->helperText('Status da Avaliação')
                    ->prefixIcon('heroicon-o-check-circle'),
                Forms\Components\FileUpload::make('file_path')
                    ->label('Modelo de Avaliação Preenchido PDF')
                    ->required()
                    ->directory('evaluation-templates')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(5120)
                    ->downloadable()
                    ->helperText('Faça upload do arquivo PDF da avaliação preenchida (máx. 5MB)')
                    ->columnSpanFull()
                    ->preserveFilenames()
                    ->previewable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('evaluation_number')
                    ->label('Período de Avaliação')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->enum([
                        'finished' => 'Finalizado',
                        'pending' => 'Pendente',
                    ])
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Baixar')
                    ->color('success')
                    ->action(function ($record) {
                        return response()->download(
                            $record->getStorageFilePath(),
                            'Avaliação Preenchida.pdf'
                        );
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
