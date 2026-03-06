<?php

namespace App\Filament\Resources\ObraSocialResource\Pages;

use App\Filament\Resources\ObraSocialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateObraSocial extends CreateRecord
{
    protected static string $resource = ObraSocialResource::class;

    public function getTitle(): string
    {
        return 'Crear Obra Social';
    }

    public function getBreadcrumb(): string
    {
        return 'Crear';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Crear');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Crear otro');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar');
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.obra-socials.create') => 'Obras Sociales',
            '' => 'Crear',
        ];
    }
}
