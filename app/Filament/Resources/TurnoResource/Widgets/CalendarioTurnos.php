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
            'locale' => 'es', // 👈 calendario en español
            'selectable' => true,
            'editable' => true,
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false, // 👈 esto fuerza formato 24h
            ],
        ];
    }

    protected function getModalFormModel(): Model|string|null
    {
        return Turno::class;
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('paciente_id')
                ->label('Paciente')
                ->relationship('paciente', 'nombre')
                ->getOptionLabelFromRecordUsing(fn ($record) =>
                    ($record->apellido ?? '') . ', ' . ($record->nombre ?? '')
                )
                ->searchable()
                ->preload()
                ->required(),

            DatePicker::make('fecha')
                ->label('Fecha del Turno')
                ->native(false)
                ->required(),

            TimePicker::make('hora')
                ->label('Hora del Turno')
                ->seconds(false)
                ->required(),

            Textarea::make('observaciones')
                ->label('Observaciones')
                ->placeholder('Agregar notas del turno, indicaciones, etc.')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    protected function getRecordData(?Model $record): array
    {
        if (! $record) return [];

        $start = $record->fecha->copy()->setTimeFromTimeString($record->hora);

        return [
            'id' => $record->id,
            'title' => $record->paciente ? $record->paciente->nombre_completo : 'Turno',
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