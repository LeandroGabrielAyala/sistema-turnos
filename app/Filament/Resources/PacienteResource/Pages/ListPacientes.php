<?php

namespace App\Filament\Resources\PacienteResource\Pages;

use App\Filament\Resources\PacienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ObraSocial;

class ListPacientes extends ListRecords
{
    protected static string $resource = PacienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Paciente'),
        ];
    }

    public function getTitle(): string
    {
        return 'Pacientes';
    }

    public function getBreadcrumb(): string
    {
        return 'Lista';
    }

    public function getTabs(): array
    {
        $tabs = [
            'todas' => Tab::make('Todas')
                ->badge($this->getModel()::count()),
        ];

        foreach (ObraSocial::all() as $obraSocial) {
            $tabs[$obraSocial->id] = Tab::make($obraSocial->alias)
                ->modifyQueryUsing(function (Builder $query) use ($obraSocial) {
                    $query->where('obra_social_id', $obraSocial->id);
                })
                ->badge(
                    $this->getModel()::where('obra_social_id', $obraSocial->id)->count()
                );
        }

        return $tabs;
    }
}
