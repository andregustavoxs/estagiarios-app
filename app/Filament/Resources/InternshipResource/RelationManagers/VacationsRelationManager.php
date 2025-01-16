<?php

namespace App\Filament\Resources\InternshipResource\RelationManagers;

use App\Models\InternVacation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
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
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('period')
                                    ->label('Período')
                                    ->options([
                                        1 => '1º Período',
                                        2 => '2º Período',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->default(1)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $totalDays = $this->ownerRecord->vacations()
                                            ->where('period', $state)
                                            ->sum('days_taken');
                                        $set('available_days', 30 - $totalDays);
                                    }),

                                Forms\Components\TextInput::make('available_days')
                                    ->label('Dias Disponíveis')
                                    ->default(function () {
                                        return 30 - $this->ownerRecord->vacations()
                                            ->where('period', 1)
                                            ->sum('days_taken');
                                    })
                                    ->disabled()
                                    ->suffix('dias')
                                    ->numeric(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Data de Início')
                                    ->required()
                                    ->format('d/m/Y')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->placeholder('Selecione a data')
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->minDate(now()->startOfDay())
                                    ->closeOnDateSelection()
                                    ->validationMessages([
                                        'min' => 'A data de início deve ser hoje ou uma data futura.',
                                    ]),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Data de Término')
                                    ->required()
                                    ->format('d/m/Y')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->placeholder('Selecione a data')
                                    ->prefixIcon('heroicon-m-calendar')
                                    ->minDate(function (Forms\Get $get) {
                                        $startDate = $get('start_date');
                                        return $startDate ? $startDate : now()->startOfDay();
                                    })
                                    ->closeOnDateSelection()
                                    ->afterOrEqual('start_date')
                                    ->validationMessages([
                                        'after_or_equal' => 'A data de término deve ser posterior ou igual à data de início.',
                                    ]),
                            ]),

                        Forms\Components\Textarea::make('observation')
                            ->label('Observação')
                            ->placeholder('Digite alguma observação (opcional)')
                            ->columnSpanFull()
                            ->rows(3)
                            ->maxLength(65535),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Férias (Limite: 30 dias por período)')
            ->description('Gerencie os períodos de férias do estagiário.')
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label('Período')
                    ->formatStateUsing(fn (int $state): string => $state . 'º Período')
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

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Data de Início')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Data de Término')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('period', 'start_date')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Férias')
                    ->modalHeading('Registrar Férias')
                    ->mutateFormDataUsing(function (array $data): array {
                        $vacation = new InternVacation([
                            'internship_id' => $this->ownerRecord->id,
                            'period' => $data['period'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                        ]);

                        // Calculate days for this vacation
                        $daysForThisVacation = $vacation->start_date->diffInDays($vacation->end_date) + 1;
                        
                        // Calculate total days including this vacation
                        $totalDays = $this->ownerRecord->vacations()
                            ->where('period', $data['period'])
                            ->sum('days_taken') + $daysForThisVacation;

                        if ($totalDays > 30) {
                            $remainingDays = 30 - $this->ownerRecord->vacations()
                                ->where('period', $data['period'])
                                ->sum('days_taken');

                            Notification::make()
                                ->danger()
                                ->title('Erro ao registrar férias')
                                ->body("Limite de dias excedido para o {$data['period']}º período. Você tem apenas {$remainingDays} dias disponíveis para férias.")
                                ->persistent()
                                ->send();

                            $this->halt();
                        }

                        if ($vacation->isOverlapping()) {
                            Notification::make()
                                ->danger()
                                ->title('Erro ao registrar férias')
                                ->body('O estagiário já possui férias registradas para estas datas. Não é possível ter dois períodos de férias simultâneos.')
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
                            'internship_id' => $this->ownerRecord->id,
                            'period' => $data['period'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                        ]);
                        $vacation->id = $record->id;
                        $vacation->exists = true;

                        // Calculate days for this vacation
                        $daysForThisVacation = $vacation->start_date->diffInDays($vacation->end_date) + 1;
                        
                        // Calculate total days including this vacation but excluding the current record
                        $totalDays = $this->ownerRecord->vacations()
                            ->where('period', $data['period'])
                            ->where('id', '!=', $record->id)
                            ->sum('days_taken') + $daysForThisVacation;

                        if ($totalDays > 30) {
                            $remainingDays = 30 - ($this->ownerRecord->vacations()
                                ->where('period', $data['period'])
                                ->where('id', '!=', $record->id)
                                ->sum('days_taken'));
                            
                            Notification::make()
                                ->danger()
                                ->title('Erro ao editar férias')
                                ->body("Limite de dias excedido para o {$data['period']}º período. Você tem apenas {$remainingDays} dias disponíveis para férias.")
                                ->persistent()
                                ->send();

                            $this->halt();
                        }

                        if ($vacation->isOverlapping()) {
                            Notification::make()
                                ->danger()
                                ->title('Erro ao editar férias')
                                ->body('O estagiário já possui férias registradas para estas datas. Não é possível ter dois períodos de férias simultâneos.')
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
