<?php

namespace App\Filament\Resources\InternshipResource\RelationManagers;

use App\Models\InternEvaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationsRelationManager extends RelationManager
{
    protected static string $relationship = 'evaluations';

    protected static ?string $title = 'Avaliações do Estagiário';

    protected static ?string $modelLabel = 'Avaliação';

    protected static ?string $pluralModelLabel = 'Avaliações';
    protected static ?string $recordTitleAttribute = 'evaluation_type';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('evaluation_number')
                            ->label('Tipo de Avaliação')
                            ->options([
                                1 => '1ª Avaliação',
                                2 => '2ª Avaliação',
                                3 => '3ª Avaliação',
                                4 => '4ª Avaliação',
                            ])
                            ->required()
                            ->native(false)
                            ->rules(fn ($record) => [
                                "unique:intern_evaluations,evaluation_number,{$record?->id},id,internship_id,{$this->ownerRecord->id}"
                            ])
                            ->validationMessages([
                                'unique' => 'Esta avaliação já foi registrada.',
                            ]),

                        Forms\Components\FileUpload::make('pdf_path')
                            ->label('PDF da Avaliação')
                            ->directory('evaluations')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->validationMessages([
                                'required' => 'O PDF da avaliação é obrigatório.',
                                'max' => 'O arquivo não pode ter mais que 5MB.',
                                'mimes' => 'O arquivo deve ser um PDF.',
                            ]),

                        Forms\Components\Toggle::make('is_completed')
                            ->label('Avaliação Concluída')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state && !$get('pdf_path')) {
                                    $set('is_completed', false);
                                    Notification::make()
                                        ->warning()
                                        ->title('PDF Obrigatório')
                                        ->body('Você precisa anexar o PDF da avaliação antes de marcá-la como concluída.')
                                        ->send();
                                }
                            }),

                        Forms\Components\View::make('filament.forms.components.template-link')
                            ->label('')
                            ->viewData([
                                'url' => '/admin/modelo-de-avaliacao',
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        $internship = $this->ownerRecord;
        $totalEvaluations = InternEvaluation::where('internship_id', $internship->id)->count();
        $completedEvaluations = InternEvaluation::where('internship_id', $internship->id)
            ->where('is_completed', true)
            ->count();

        $period = sprintf(
            "Período: %s - %s",
            $internship->start_date?->format('d/m/Y') ?? 'N/A',
            $internship->end_date?->format('d/m/Y') ?? 'N/A'
        );

        $progressIndicators = array_map(
            fn ($i) => $i <= $completedEvaluations ? '●' : '○',
            range(1, 4)
        );

        return $table
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->description(fn () => "{$period} • " . implode(' ', $progressIndicators) . " ({$completedEvaluations}/4 Avaliações Concluídas)")
            ->columns([
                Tables\Columns\TextColumn::make('evaluation_type')
                    ->label('Tipo')
                    ->sortable(['evaluation_number']),

                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor(Color::Green)
                    ->falseColor(Color::Red),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('evaluation_number')
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
