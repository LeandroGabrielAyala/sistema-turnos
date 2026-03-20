<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return "Editar Usuario: {$this->record->name}";
    }

    public function getBreadcrumb(): string
    {
        return 'Editar';
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.users.index') => 'Usuarios',
            '#' => "{$this->record->name}",
            '' => 'Editar',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->modalHeading('Eliminar usuario')
                ->modalDescription('¿Está seguro de eliminar este usuario?')
                ->modalSubmitActionLabel('Sí, eliminar')
                ->modalCancelActionLabel('Cancelar')
                ->successNotificationTitle('Usuario eliminado correctamente'),
            Actions\EditAction::make()
                ->label('Editar')
                ->modalHeading('Editar usuario')
                ->modalDescription('¿Está seguro de editar este usuario?')
                ->modalSubmitActionLabel('Sí, editar')
                ->modalCancelActionLabel('Cancelar')
                ->successNotificationTitle('Usuario actualizado correctamente'),
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
