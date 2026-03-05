<?php

namespace App\Filament\Resources\TurnoResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Model;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;

class CalendarioTurnos extends FullCalendarWidget
{
    public Model|string|null $model = Turno::class;

    public function config(): array
    {
        return [
            'selectable' => true,
            'editable' => true,

            // Correcto para v3: detecta selección de días vacíos
            'select' => [
                'js' => <<<JS
                    function(info) {
                        const url = "/filament/resources/turnos/create?fecha=" + info.startStr + "&hora=09:00";
                        window.location.href = url;
                    }
                JS
            ],
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('paciente_id')
                ->relationship('paciente', 'nombre')
                ->required(),

            DatePicker::make('fecha')->required(),
            TimePicker::make('hora')->required(),
            Textarea::make('observaciones'),
        ];
    }

    protected function getRecordData(?Model $record): array
    {
        if (! $record) {
            return [];
        }

        $start = $record->fecha->copy()->setTimeFromTimeString($record->hora);

        return [
            'id' => $record->id,
            'title' => $record->paciente->nombre ?? 'Turno',
            'start' => $start->toIso8601String(),
            'end' => $start->copy()->addMinutes(30)->toIso8601String(),
        ];
    }

    public function fetchEvents(array $info): array
    {
        return Turno::with('paciente')
            ->whereBetween('fecha', [
                substr($info['start'], 0, 10),
                substr($info['end'], 0, 10),
            ])
            ->get()
            ->map(fn ($turno) => $this->getRecordData($turno))
            ->toArray();
    }
}