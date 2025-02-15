<?php

namespace App\Filament\Resources\InternshipResource\RelationManagers;

use App\Models\InternAddendum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Colors\Color;

class AddendumsRelationManager extends RelationManager
{
    protected static string $relationship = 'addendums';

    protected static ?string $title = 'Termos Aditivos';

    protected static ?string $modelLabel = 'Termo Aditivo';

    protected static ?string $pluralModelLabel = 'Termos Aditivos';

    protected static ?string $recordTitleAttribute = 'addendum_type';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->hasFirstEvaluation();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('addendum_number')
                            ->label('Tipo de Aditivo')
                            ->options([
                                1 => '1º Aditivo',
                                2 => '2º Aditivo',
                                3 => '3º Aditivo',
                            ])
                            ->required()
                            ->native(false)
                            ->rules(fn ($record) => [
                                "unique:intern_addendums,addendum_number,{$record?->id},id,internship_id,{$this->ownerRecord->id}"
                            ])
                            ->validationMessages([
                                'unique' => 'Este aditivo já foi registrado.',
                            ]),

                        Forms\Components\FileUpload::make('pdf_path')
                            ->label('PDF do Aditivo')
                            ->directory('addendums')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->downloadable()
                            ->openable()
                            ->validationMessages([
                                'required' => 'O PDF do aditivo é obrigatório.',
                                'max' => 'O arquivo não pode ter mais que 5MB.',
                                'mimes' => 'O arquivo deve ser um PDF.',
                            ]),

                        Forms\Components\Toggle::make('is_completed')
                            ->label('Aditivo Concluído')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state && !$get('pdf_path')) {
                                    $set('is_completed', false);
                                    Notification::make()
                                        ->warning()
                                        ->title('PDF Obrigatório')
                                        ->body('Você precisa anexar o PDF do aditivo antes de marcá-lo como concluído.')
                                        ->send();
                                }
                            }),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        $internship = $this->ownerRecord;
        $totalAddendums = InternAddendum::where('internship_id', $internship->id)->count();
        $completedAddendums = InternAddendum::where('internship_id', $internship->id)
            ->where('is_completed', true)
            ->count();

        $period = sprintf(
            "Período: %s - %s",
            $internship->start_date?->format('d/m/Y') ?? 'N/A',
            $internship->end_date?->format('d/m/Y') ?? 'N/A'
        );

        $progressIndicators = array_map(
            fn ($i) => $i <= $completedAddendums ? '●' : '○',
            range(1, 3)
        );

        return $table
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->description(fn () => "{$period} • " . implode(' ', $progressIndicators) . " ({$completedAddendums}/3 Aditivos Concluídos)")
            ->columns([
                Tables\Columns\TextColumn::make('addendum_type')
                    ->label('Tipo')
                    ->sortable(['addendum_number']),

                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor(Color::Green)
                    ->falseColor(Color::Red),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('addendum_number')
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
}
