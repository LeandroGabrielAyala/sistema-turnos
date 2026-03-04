<?php

namespace App\Filament\Resources\TurnoResource\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Turno;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Carbon\Carbon;

class CalendarioTurnos extends FullCalendarWidget
{
    public Model|string|null $model = Turno::class;

    protected static array $config = [
        'selectable' => true,
        'editable' => true,
    ];

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

    /**
     * Esto convierte TU modelo al formato que FullCalendar necesita
     */
    protected function getRecordData(Model $record): array
    {
        return [
            'id' => $record->id,
            'title' => $record->paciente->nombre ?? 'Turno',
            'start' => Carbon::parse($record->fecha . ' ' . $record->hora)->toIso8601String(),
            'end' => Carbon::parse($record->fecha . ' ' . $record->hora)
                ->addMinutes(30)
                ->toIso8601String(),
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