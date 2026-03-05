<?php

namespace App\Filament\Resources\TurnoResource\Pages;

use App\Filament\Resources\TurnoResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTurno extends CreateRecord
{
    protected static string $resource = TurnoResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Turno Creado')
            ->body('El turno fue registrado exitosamente.')
            ->success();
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (request()->has('fecha')) {
            $data['fecha'] = request('fecha');
        }

        if (request()->has('hora')) {
            $data['hora'] = request('hora');
        }

        return $data;
    }
    
    public function getTitle(): string
    {
        return 'Crear Turno';
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
}
