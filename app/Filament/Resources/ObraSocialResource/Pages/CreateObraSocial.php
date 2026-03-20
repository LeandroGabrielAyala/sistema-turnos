<?php

namespace App\Filament\Resources\ObraSocialResource\Pages;

use App\Filament\Resources\ObraSocialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateObraSocial extends CreateRecord
{
    protected static string $resource = ObraSocialResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Obra Social Creada')
            ->body('La obra social fue registrada exitosamente.')
            ->success();
    }

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
