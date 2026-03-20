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
                        'Turno del ' . $record->fecha . ' - ' . $record->hora
                    )
                    ->modalWidth('lg')
                    ->infolist([
                        Tabs::make('Turno')
                            ->tabs([

                                Tab::make('Información')
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        TextEntry::make('fecha')
                                            ->label('📅 Fecha')
                                            ->date('d/m/Y'),

                                        TextEntry::make('hora')
                                            ->label('🕐 Hora'),

                                        TextEntry::make('estado')
                                            ->label('📌 Estado')
                                            ->badge(),

                                        TextEntry::make('paciente.nombre_completo')
                                            ->label('👤 Paciente'),
                                    ])
                                    ->columns(2),

                            ]),
                    ]),
            ]);
    }
}