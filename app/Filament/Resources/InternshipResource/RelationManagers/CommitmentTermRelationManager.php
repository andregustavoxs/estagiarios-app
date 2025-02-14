<?php

namespace App\Filament\Resources\InternshipResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommitmentTermRelationManager extends RelationManager
{
    protected static string $relationship = 'commitmentTerm';
    protected static ?string $title = 'Termo de Compromisso';
    protected static ?string $modelLabel = 'Termo de Compromisso';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Status das Assinaturas')
                    ->description('Marque as assinaturas que já foram coletadas')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('intern_signature')
                                    ->label('Assinatura do Estagiário')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $set('intern_signature_date', now());
                                        } else {
                                            $set('intern_signature_date', null);
                                        }
                                    }),

                                Forms\Components\Toggle::make('court_signature')
                                    ->label('Assinatura do Tribunal')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $set('court_signature_date', now());
                                        } else {
                                            $set('court_signature_date', null);
                                        }
                                    }),

                                Forms\Components\Toggle::make('institution_signature')
                                    ->label('Assinatura da Instituição')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $set('institution_signature_date', now());
                                        } else {
                                            $set('institution_signature_date', null);
                                        }
                                    }),
                            ]),
                    ]),

                    Forms\Components\Section::make('Documento')
                        ->description('Upload do termo de compromisso assinado')
                        ->schema([
                            Forms\Components\FileUpload::make('document_path')
                                ->label('Termo de Compromisso')
                                ->acceptedFileTypes(['application/pdf'])
                                ->helperText('Faça upload do termo de compromisso em formato PDF.')
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if (!$state) return;

                                    $file = $state;

                                    $client = new \GuzzleHttp\Client();

                                    try {
                                        $response = $client->post('https://int.tce.ma.gov.br/s3aws/api/files/upload', [
                                            'multipart' => [
                                                [
                                                    'name' => 'file',
                                                    'contents' => fopen($file->getRealPath(), 'r'),
                                                    'filename' => $file->getClientOriginalName()
                                                ],
                                                [
                                                    'name' => 'bucketName',
                                                    'contents' => 'teste.tcema.tc.br'
                                                ],
                                                [
                                                    'name' => 'pathWithoutFilename',
                                                    'contents' => 'termo_compromisso'
                                                ],
                                                [
                                                    'name' => 'filename',
                                                    'contents' => $file->getClientOriginalName()
                                                ]
                                            ]
                                        ]);

                                        $result = json_decode($response->getBody()->getContents(), true);


                                        // Mantém o estado original do arquivo e salva o filePath em um campo separado
                                        return $result['filePath'] ?? null;

                                    } catch (\Exception $e) {
                                        \Illuminate\Support\Facades\Log::error('Erro ao fazer upload do arquivo:', [
                                            'error' => $e->getMessage(),
                                            'trace' => $e->getTraceAsString()
                                        ]);

                                        \Filament\Notifications\Notification::make()
                                            ->danger()
                                            ->title('Erro ao fazer upload')
                                            ->body($e->getMessage())
                                            ->send();

                                        return null;
                                    }
                                }),
                            Forms\Components\Hidden::make('document_path'),
                        ])
                    ->hidden(function (Forms\Get $get) {
                        return !($get('intern_signature') &&
                               $get('court_signature') &&
                               $get('institution_signature'));
                    }),

                Forms\Components\Section::make('Observações')
                    ->schema([
                        Forms\Components\Textarea::make('observations')
                            ->label('Observações')
                            ->placeholder('Adicione observações relevantes sobre o termo de compromisso')
                            ->rows(3),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('intern_signature')
                    ->label('Assinatura do Estagiário')
                    ->boolean(),

                Tables\Columns\IconColumn::make('court_signature')
                    ->label('Assinatura do Tribunal')
                    ->boolean(),

                Tables\Columns\IconColumn::make('institution_signature')
                    ->label('Assinatura da Instituição')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('lg'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('lg'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn ($record) => $record->document_path ? storage_path('app/public/' . $record->document_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->document_path !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
