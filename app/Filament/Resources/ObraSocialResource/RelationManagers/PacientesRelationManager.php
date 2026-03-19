<?php

namespace App\Filament\Resources\ObraSocialResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;

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
                ViewAction::make()->label('Ver'),
            ]);
    }
}