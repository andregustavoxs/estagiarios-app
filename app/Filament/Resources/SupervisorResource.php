<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupervisorResource\Pages;
use App\Filament\Resources\SupervisorResource\RelationManagers;
use App\Models\Supervisor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class SupervisorResource extends Resource
{
    protected static ?string $model = Supervisor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Supervisor';
    
    protected static ?string $pluralModelLabel = 'Supervisores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'sm' => 3,
                ])
                    ->schema([
                        Forms\Components\Section::make('Informações do Supervisor')
                            ->description('Dados do supervisor')
                            ->icon('heroicon-o-user')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite o nome completo do supervisor')
                                    ->helperText('Nome completo do supervisor')
                                    ->prefixIcon('heroicon-o-user')
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este nome de supervisor já está em uso.',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make('Foto')
                            ->description('Foto de identificação do supervisor')
                            ->icon('heroicon-o-camera')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Foto do Perfil')
                                    ->image()
                                    ->directory('supervisors')
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->helperText('Faça upload de uma foto de identificação')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('interns_count')
                    ->label('Qtd. Estagiários')
                    ->counts('interns')
                    ->sortable()
                    ->icon('heroicon-m-user-group')
                    ->alignEnd(),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($action, Supervisor $record) {
                        if ($record->interns()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Ação bloqueada')
                                ->body('Não é possível excluir este supervisor pois existem estagiários vinculados a ele.')
                                ->send();
                            
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($action, Collection $records) {
                            foreach ($records as $record) {
                                if ($record->interns()->count() > 0) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Ação bloqueada')
                                        ->body('Não é possível excluir supervisores que possuem estagiários vinculados.')
                                        ->send();
                                    
                                    $action->cancel();
                                    return;
                                }
                            }
                        }),
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
            'index' => Pages\ListSupervisors::route('/'),
            'create' => Pages\CreateSupervisor::route('/create'),
            'edit' => Pages\EditSupervisor::route('/{record}/edit'),
        ];
    }
}
