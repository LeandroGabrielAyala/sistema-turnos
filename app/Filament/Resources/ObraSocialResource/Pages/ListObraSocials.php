<?php

namespace App\Filament\Resources\ObraSocialResource\Pages;

use App\Filament\Resources\ObraSocialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListObraSocials extends ListRecords
{
    protected static string $resource = ObraSocialResource::class;


    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.obra-socials.index') => 'Obras Sociales',
            '' => 'Lista',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Obra Social'),
        ];
    }

    public function getTitle(): string
    {
        return 'Lista de obras sociales';
    }

    public function getBreadcrumb(): string
    {
        return 'Lista';
    }
}
