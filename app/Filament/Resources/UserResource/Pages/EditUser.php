<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Usuario Editado')
            ->body('El usuario fue actualizado exitosamente.')
            ->success();
    }

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
