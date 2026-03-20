<?php

namespace App\Filament\Resources\TurnoResource\Pages;

use App\Filament\Resources\TurnoResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTurno extends EditRecord
{
    protected static string $resource = TurnoResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Turno Editado')
            ->body('El turno fue actualizado exitosamente.')
            ->success();
    }

    public function getTitle(): string
    {
        return "Editar Turno: {$this->record->paciente->apellido}, {$this->record->paciente->nombre}";
    }

    public function getBreadcrumb(): string
    {
        return 'Editar';
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.turnos.index') => 'Turnos',
            '#' => "{$this->record->paciente->apellido}, {$this->record->paciente->nombre}",
            '' => 'Editar',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Guardar cambios');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar');
    }
}
