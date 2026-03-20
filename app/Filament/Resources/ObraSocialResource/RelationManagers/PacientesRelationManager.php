<?php

namespace App\Filament\Resources\ObraSocialResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;

class PacientesRelationManager extends RelationManager
{
    protected static string $relationship = 'pacientes';

    protected static ?string $title = 'Pacientes';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('apellido')->label('Apellido'),
                TextColumn::make('nombre')->label('Nombre'),
                TextColumn::make('dni')->label('DNI'),
            ])

            ->actions([
                ViewAction::make()
                    ->label('Ver')
                    ->modalHeading(fn ($record) =>
                        'Paciente: ' . $record->apellido . ' ' . $record->nombre
                    )
                    ->modalWidth('lg')
                    ->infolist([
                        Tabs::make('Paciente')
                            ->tabs([

                                Tab::make('Datos')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        TextEntry::make('apellido')->label('Apellido'),
                                        TextEntry::make('nombre')->label('Nombre'),
                                        TextEntry::make('dni')->label('DNI'),
                                        TextEntry::make('telefono')->label('Teléfono'),
                                    ])
                                    ->columns(2),

                            ]),
                    ]),
            ]);
    }
}