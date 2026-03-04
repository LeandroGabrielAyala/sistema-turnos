<?php

namespace App\Filament\Resources\TurnoResource\Pages;

use App\Filament\Resources\TurnoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTurno extends CreateRecord
{
    protected static string $resource = TurnoResource::class;

    protected function afterCreate(): void
    {
        \Filament\Notifications\Notification::make()
            ->title('Turno creado correctamente')
            ->success()
            ->send();
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

    protected function getFormDefaults(): array
    {
        return [
            'fecha' => request('fecha'),
            'hora' => request('hora'),
        ];
    }
}
