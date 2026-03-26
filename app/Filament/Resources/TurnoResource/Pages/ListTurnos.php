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
    return [

        'hoy' => Tab::make('Hoy')
            ->modifyQueryUsing(function (Builder $query) {

                $query->whereDate(
                    'fecha',
                    Carbon::today()
                );
            })
            ->badge(fn () =>
                \App\Models\Turno::whereDate(
                    'fecha',
                    Carbon::today()
                )->count()
            )
            ->badgeColor('success'),

        'proximos' => Tab::make('Próximos')
            ->modifyQueryUsing(function (Builder $query) {

                $query->where(function ($q) {

                    $q->whereDate('fecha', '>', Carbon::today())

                      ->orWhere(function ($q2) {

                          $q2->whereDate('fecha', Carbon::today())
                             ->whereTime('hora', '>', Carbon::now()->format('H:i:s'));

                      });

                });

            })
            ->badgeColor('warning'),

        'anteriores' => Tab::make('Anteriores')
            ->modifyQueryUsing(function (Builder $query) {

                $query->where(function ($q) {

                    $q->whereDate('fecha', '<', Carbon::today())

                      ->orWhere(function ($q2) {

                          $q2->whereDate('fecha', Carbon::today())
                             ->whereTime('hora', '<', Carbon::now()->format('H:i:s'));

                      });

                });

            })
            ->badgeColor('gray'),

    ];
}
}