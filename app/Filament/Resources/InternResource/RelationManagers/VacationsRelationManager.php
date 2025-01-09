<?php

namespace App\Filament\Resources\InternResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use App\Models\InternVacation;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class VacationsRelationManager extends RelationManager
{
    protected static string $relationship = 'vacations';
    protected static ?string $title = 'Férias';
    protected static ?string $modelLabel = 'Férias';
    protected static ?string $pluralModelLabel = 'Férias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Data de Início')
                            ->required()
                            ->format('d/m/Y')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->placeholder('dd/mm/aaaa')
                            ->helperText('Data de início das férias')
                            ->prefixIcon('heroicon-o-calendar')
                            ->minDate(now()->startOfDay())
                            ->validationMessages([
                                'min' => 'A data de início deve ser hoje ou uma data futura.',
                            ]),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Data de Término')
                            ->required()
                            ->format('d/m/Y')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->placeholder('dd/mm/aaaa')
                            ->helperText('Data de término das férias')
                            ->prefixIcon('heroicon-o-calendar')
                            ->minDate(function (Forms\Get $get) {
                                $startDate = $get('start_date');
                                return $startDate ? $startDate : now()->startOfDay();
                            })
                            ->afterOrEqual('start_date')
                            ->validationMessages([
                                'after_or_equal' => 'A data de término deve ser posterior ou igual à data de início.',
                            ]),
                    ]),

                Forms\Components\Textarea::make('observation')
                    ->label('Observação')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->placeholder('Digite alguma observação (opcional)')
                    ->helperText('Observações adicionais sobre as férias'),
            ]);
    }

    public function table(Table $table): Table
    {
        $totalDaysTaken = $this->ownerRecord->vacations()->sum('days_taken');
        $hasReachedLimit = $totalDaysTaken >= 30;

        return $table
            ->heading('Férias (Limite: 30 dias)')
            ->description($hasReachedLimit ? 'Limite de 30 dias de férias atingido.' : 'Gerencie os períodos de férias do estagiário.')
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Data de Início')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Data de Término')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_taken')
                    ->label('Dias')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('vacation_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Férias futuras' => 'gray',
                        'Férias concluídas' => 'success',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('observation')
                    ->label('Observação')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        if (strlen($record->observation) > 50) {
                            return $record->observation;
                        }
                        return null;
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Férias')
                    ->modalHeading('Registrar Férias')
                    ->hidden(fn () => $this->ownerRecord->vacations()->sum('days_taken') >= 30)
                    ->mutateFormDataUsing(function (array $data): array {
                        $vacation = new InternVacation([
                            'intern_id' => $this->ownerRecord->id,
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                        ]);

                        // Calculate days for this vacation
                        $daysForThisVacation = $vacation->start_date->diffInDays($vacation->end_date) + 1;
                        
                        // Calculate total days including this vacation
                        $totalDays = $this->ownerRecord->vacations()->sum('days_taken') + $daysForThisVacation;

                        if ($totalDays > 30) {
                            $remainingDays = 30 - $this->ownerRecord->vacations()->sum('days_taken');
                            Notification::make()
                                ->danger()
                                ->title('Erro ao registrar férias')
                                ->body("Limite de dias excedido. Você tem apenas {$remainingDays} dias disponíveis para férias.")
                                ->persistent()
                                ->send();

                            $this->halt();
                        }

                        if ($vacation->isOverlapping()) {
                            Notification::make()
                                ->danger()
                                ->title('Erro ao registrar férias')
                                ->body('Já existe um período de férias registrado para estas datas.')
                                ->persistent()
                                ->send();

                            $this->halt();
                        }

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Férias registradas')
                            ->body('As férias foram registradas com sucesso.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar Férias')
                    ->mutateFormDataUsing(function (InternVacation $record, array $data): array {
                        $vacation = new InternVacation([
                            'intern_id' => $this->ownerRecord->id,
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                        ]);
                        $vacation->id = $record->id;
                        $vacation->exists = true;

                        // Calculate days for this vacation
                        $daysForThisVacation = $vacation->start_date->diffInDays($vacation->end_date) + 1;
                        
                        // Calculate total days including this vacation but excluding the current record
                        $totalDays = $this->ownerRecord->vacations()
                            ->where('id', '!=', $record->id)
                            ->sum('days_taken') + $daysForThisVacation;

                        if ($totalDays > 30) {
                            $remainingDays = 30 - ($this->ownerRecord->vacations()
                                ->where('id', '!=', $record->id)
                                ->sum('days_taken'));
                            
                            Notification::make()
                                ->danger()
                                ->title('Erro ao editar férias')
                                ->body("Limite de dias excedido. Você tem apenas {$remainingDays} dias disponíveis para férias.")
                                ->persistent()
                                ->send();

                            $this->halt();
                        }

                        if ($vacation->isOverlapping()) {
                            Notification::make()
                                ->danger()
                                ->title('Erro ao editar férias')
                                ->body('Já existe um período de férias registrado para estas datas.')
                                ->persistent()
                                ->send();

                            $this->halt();
                        }

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Excluir Férias'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Excluir Férias'),
                ]),
            ]);
    }

    public function halt()
    {
        throw ValidationException::withMessages([
            'data' => ['Operação não permitida.'],
        ]);
    }
}
