<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Paciente;
use App\Models\ObraSocial;

class EstadisticasPacientes extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pacientes', Paciente::count()),

            Stat::make('Con Obra Social', 
                Paciente::whereNotNull('obra_social_id')->count()),

            Stat::make('Obras Sociales',
                ObraSocial::count()),
        ];
    }
}
