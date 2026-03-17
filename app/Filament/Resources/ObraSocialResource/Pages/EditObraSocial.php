<?php

namespace App\Filament\Resources\ObraSocialResource\Pages;

use App\Filament\Resources\ObraSocialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditObraSocial extends EditRecord
{
    protected static string $resource = ObraSocialResource::class;

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
