<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

use App\Models\Paciente;
use App\Models\ObraSocial;
use App\Models\Turno;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [

            Card::make('Turnos', Turno::count())
                ->description('Total agendados')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            Card::make('Pacientes', Paciente::count())
                ->description('Total registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Card::make('Obras Sociales', ObraSocial::count())
                ->description('Total cargadas')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

        ];
    }
}