<?php

namespace App\Filament\Resources\PacienteResource\Pages;

use App\Filament\Resources\PacienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditPaciente extends EditRecord
{
    protected static string $resource = PacienteResource::class;

    public function getTitle(): string
    {
        return "Editar Paciente: {$this->record->apellido}, {$this->record->nombre}";
    }

    public function getBreadcrumb(): string
    {
        return 'Editar';
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.pacientes.index') => 'Pacientes',
            '#' => "{$this->record->apellido}, {$this->record->nombre}",
            '' => 'Editar',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->modalHeading('Eliminar paciente')
                ->modalDescription('¿Está seguro de eliminar este paciente?')
                ->modalSubmitActionLabel('Sí, eliminar')
                ->modalCancelActionLabel('Cancelar')
                ->successNotificationTitle('Paciente eliminado correctamente'),
                
            Actions\EditAction::make()
                ->label('Editar')
                ->modalHeading('Editar paciente')
                ->modalDescription('¿Está seguro de editar este paciente?')
                ->modalSubmitActionLabel('Sí, editar')
                ->modalCancelActionLabel('Cancelar')
                ->successNotificationTitle('Paciente actualizado correctamente'),
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['presion_sistolica']) && !empty($data['presion_diastolica'])) {
            $data['presion_arterial'] = $data['presion_sistolica'] . '/' . $data['presion_diastolica'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!empty($data['presion_arterial']) && str_contains($data['presion_arterial'], '/')) {
            [$sistolica, $diastolica] = explode('/', $data['presion_arterial']);

            $data['presion_sistolica'] = $sistolica;
            $data['presion_diastolica'] = $diastolica;
        }

        return $data;
    }
}
