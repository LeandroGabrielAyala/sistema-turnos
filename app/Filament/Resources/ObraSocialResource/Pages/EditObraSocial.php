<?php

namespace App\Filament\Resources\ObraSocialResource\Pages;

use App\Filament\Resources\ObraSocialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditObraSocial extends EditRecord
{
    protected static string $resource = ObraSocialResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Obra Social Editada')
            ->body('La obra social fue actualizada exitosamente.')
            ->success();
    }

    public function getTitle(): string
    {
        return "Editar Obra Social: {$this->record->alias} - {$this->record->nombre}";
    }

    public function getBreadcrumb(): string
    {
        return 'Editar';
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.obra-socials.index') => 'Obras Sociales',
            '#' => "{$this->record->alias}",
            '' => 'Editar',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->modalHeading('Eliminar obra social')
                ->modalDescription('¿Está seguro de eliminar esta obra social?')
                ->modalSubmitActionLabel('Sí, eliminar')
                ->modalCancelActionLabel('Cancelar')
                ->successNotificationTitle('Obra social eliminada correctamente'),
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
