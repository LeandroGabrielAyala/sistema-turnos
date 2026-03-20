<?php

namespace App\Filament\Resources\PacienteResource\Pages;

use App\Filament\Resources\PacienteResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePaciente extends CreateRecord
{
    protected static string $resource = PacienteResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Paciente Creado')
            ->body('El paciente fue registrado exitosamente.')
            ->success();
    }

    public function getTitle(): string
    {
        return 'Crear Paciente';
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['presion_sistolica']) && !empty($data['presion_diastolica'])) {
            $data['presion_arterial'] = $data['presion_sistolica'] . '/' . $data['presion_diastolica'];
        }

        return $data;
    }
}
