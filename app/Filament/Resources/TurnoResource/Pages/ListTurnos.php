<?php

namespace App\Filament\Resources\TurnoResource\Pages;

use App\Filament\Resources\TurnoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListTurnos extends ListRecords
{
    protected static string $resource = TurnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Turno'),
        ];
    }

    public function getTitle(): string
    {
        return 'Turnos';
    }

    public function getBreadcrumb(): string
    {
        return 'Lista';
    }

    public function getTabs(): array
    {
        $today = Carbon::today();

        return [

            'hoy' => Tab::make('Hoy')
                ->modifyQueryUsing(function (Builder $query) use ($today) {
                    $query->whereDate('fecha', $today);
                })
                ->badge(fn () =>
                    \App\Models\Turno::whereDate('fecha', $today)->count()
                )
                ->badgeColor('success'),

            'anteriores' => Tab::make('Anteriores')
                ->modifyQueryUsing(function (Builder $query) use ($today) {
                    $query->whereDate('fecha', '<', $today);
                })
                ->badge(fn () =>
                    \App\Models\Turno::whereDate('fecha', '<', $today)->count()
                )
                ->badgeColor('gray'),

            'proximos' => Tab::make('Próximos')
                ->modifyQueryUsing(function (Builder $query) use ($today) {
                    $query->whereDate('fecha', '>', $today);
                })
                ->badge(fn () =>
                    \App\Models\Turno::whereDate('fecha', '>', $today)->count()
                )
                ->badgeColor('warning'),

        ];
    }
}