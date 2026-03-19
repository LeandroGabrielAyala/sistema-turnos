<?php

namespace App\Filament\Resources\ObraSocialResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;

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
                ViewAction::make()->label('Ver'),
            ]);
            // ->filters([
            //     SelectFilter::make('estado')
            //         ->options([
            //             'pendiente' => 'Pendiente',
            //             'confirmado' => 'Confirmado',
            //             'cancelado' => 'Cancelado',
            //             'atendido' => 'Atendido',
            //         ])
            // ]);
    }
}