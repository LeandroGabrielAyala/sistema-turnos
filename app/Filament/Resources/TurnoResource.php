<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TurnoResource\Pages;
use App\Models\Turno;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Resources\TurnoResource\Widgets;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class TurnoResource extends Resource
{
    protected static ?string $model = Turno::class;

    public static function getWidgets(): array
    {
        return [
            Widgets\CalendarioTurnos::class,
        ];
    }

    protected static ?string $navigationGroup = 'Agenda';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('paciente_id')
                    ->relationship(
                        name: 'paciente',
                        titleAttribute: 'nombre'
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "{$record->apellido}, {$record->nombre}"
                    )
                    ->searchable(['nombre', 'apellido'])
                    ->required(),

                DatePicker::make('fecha')
                    ->required()
                    ->minDate(now()),

                TimePicker::make('hora')
                    ->seconds(false)
                    ->required(),

                Select::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'cancelado' => 'Cancelado',
                        'atendido' => 'Atendido',
                    ])
                    ->default('pendiente')
                    ->required(),

                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('fecha')
                ->date()
                ->sortable(),

            TextColumn::make('hora')
                ->sortable(),

            TextColumn::make('paciente.nombre_completo')
                ->searchable(),

            BadgeColumn::make('estado')
                ->colors([
                    'warning' => 'pendiente',
                    'success' => 'confirmado',
                    'danger' => 'cancelado',
                    'primary' => 'atendido',
                ]),
        ])
        ->defaultSort('fecha', 'desc');
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTurnos::route('/'),
            'create' => Pages\CreateTurno::route('/create'),
            'edit' => Pages\EditTurno::route('/{record}/edit'),
            'calendario' => Pages\CalendarioTurnos::route('/calendario'),
        ];
    }
}
