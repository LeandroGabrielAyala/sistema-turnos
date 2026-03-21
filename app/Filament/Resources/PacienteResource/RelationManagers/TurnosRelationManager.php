<?php

namespace App\Filament\Resources\ObraSocialResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;

class TurnosRelationManager extends RelationManager
{
    protected static string $relationship = 'turnos';

    protected static ?string $title = 'Turnos';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha', 'desc')

            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y'),

                TextColumn::make('hora')
                    ->label('Hora'),

                // TextColumn::make('paciente.nombre_completo')
                //     ->label('Paciente'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pendiente' => 'warning',
                        'confirmado' => 'success',
                        'cancelado' => 'danger',
                        'atendido' => 'primary',
                    }),
            ])

            ->actions([
                ViewAction::make()
                    ->label('Ver')
                    ->modalHeading(fn ($record) =>
                        'Turno del ' . \Carbon\Carbon::parse($record->fecha)->format('d/m/Y') 
                        . ' - ' . $record->hora
                    )
                    ->modalWidth('lg')
                    ->infolist([
                        Tabs::make('Turno')
                            ->tabs([

                                Tab::make('informacion')
                                    ->label(fn ($record) => 
                                        'Información - ' . ucfirst($record->estado)
                                    )
                                    ->icon('heroicon-o-calendar')
                                    ->schema([

                                        TextEntry::make('fecha')
                                            ->label('◾ Fecha')
                                            ->date('d/m/Y'),

                                        TextEntry::make('hora')
                                            ->label('◾ Hora'),

                                        // 🔥 NUEVO
                                        TextEntry::make('motivo_consulta')
                                            ->label('◾ Motivo del turno')
                                            ->columnSpanFull(),

                                        // 🔥 SOLO SI FUE ATENDIDO
                                        TextEntry::make('observacion_medica')
                                            ->label('◾ Observación médica')
                                            ->visible(fn ($record) => $record->estado === 'atendido')
                                            ->columnSpanFull(),

                                        // 🔥 ESTUDIOS (PRO)
                                        TextEntry::make('estudios_formateados')
                                            ->label('◾ Estudios')
                                            ->visible(fn ($record) => $record->estado === 'atendido')
                                            ->badge()
                                            ->separator(',')
                                            ->formatStateUsing(function ($state) {
                                                return $state ?: 'Sin estudios recomendados';
                                            })->columnSpanFull(),

                                    ])
                                    ->columns(2),

                            ]),
                    ]),
            ]);
    }
}