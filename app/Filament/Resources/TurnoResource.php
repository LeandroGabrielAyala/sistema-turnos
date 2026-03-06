<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TurnoResource\Pages;
use App\Filament\Resources\TurnoResource\Widgets\CalendarioTurnos;
use App\Models\Turno;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class TurnoResource extends Resource
{
    protected static ?string $model = Turno::class;
    protected static ?string $modelLabel = 'Turno';
    protected static ?string $pluralModelLabel = 'Turnos';
    
    protected static ?string $navigationGroup = 'Agenda';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Turnos';
    protected static ?string $slug = 'turnos';

    protected static ?string $recordTitleAttribute = 'fecha';

    // 🔎 Búsqueda global
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'fecha',
            'hora',
            'estado',
            'paciente.nombre',
            'paciente.apellido',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->paciente->apellido . ', ' . $record->paciente->nombre;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Fecha' => $record->fecha->format('d/m/Y'),
            'Hora' => \Carbon\Carbon::parse($record->hora)->format('H:i'),
            'Estado' => ucfirst($record->estado),
        ];
    }

    // ✅ Widgets del calendario
    public static function getWidgets(): array
    {
        return [
            CalendarioTurnos::class,
        ];
    }

    // ✅ Formulario para crear/editar turnos
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('paciente_id')
                    ->label('Paciente')
                    ->options(
                        \App\Models\Paciente::query()
                            ->orderBy('apellido')
                            ->get()
                            ->mapWithKeys(fn ($p) => [
                                $p->id => $p->apellido . ', ' . $p->nombre
                            ])
                    )
                    ->searchable()
                    ->required(),

                DatePicker::make('fecha')
                    ->required()
                    ->minDate(now()),

                TimePicker::make('hora')
                    ->seconds(false)
                    ->required(),

                Select::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'cancelado' => 'Cancelado',
                        'atendido' => 'Atendido',
                    ])
                    ->default('pendiente')
                    ->required(),

                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

    // ✅ Tabla de turnos
    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('hora')
                    ->sortable(),

                TextColumn::make('paciente.nombre_completo')
                    ->label('Paciente')
                    ->searchable(['pacientes.nombre', 'pacientes.apellido'])
                    ->sortable(query: function (Builder $query, string $direction) {
                        $query->orderBy(
                            \App\Models\Paciente::select('apellido')
                                ->whereColumn('pacientes.id', 'turnos.paciente_id'),
                            $direction
                        )->orderBy(
                            \App\Models\Paciente::select('nombre')
                                ->whereColumn('pacientes.id', 'turnos.paciente_id'),
                            $direction
                        );
                    }),

                BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'success' => 'confirmado',
                        'danger' => 'cancelado',
                        'primary' => 'atendido',
                    ]),
            ])

            ->filters([

                // 🔹 Filtro por estado
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'cancelado' => 'Cancelado',
                        'atendido' => 'Atendido',
                    ]),

                // 🔹 Filtro por paciente
                SelectFilter::make('paciente_id')
                    ->relationship('paciente', 'apellido'),

                // 🔹 Filtro por fecha específica
                Filter::make('fecha')
                    ->form([
                        DatePicker::make('fecha'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['fecha']) {
                            $query->whereDate('fecha', $data['fecha']);
                        }
                    }),

                Filter::make('proximos')
                    ->label('Próximos 7 días')
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['isActive']) {
                            return $query;
                        }

                        return $query->whereBetween('fecha', [
                            today(),
                            today()->addDays(7),
                        ]);
                    })
                    ->toggle(),
            ])

            ->actions([
                ViewAction::make()
                    ->label('Ver')
                    ->modalHeading(fn ($record) => 
                        'Detalle del Turno - ' . $record->fecha->format('d/m/Y') . ' ' . $record->hora
                    )
                    ->modalWidth('4xl')
                    ->infolist([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tab::make('Estado')
                                    ->icon('heroicon-o-bookmark')
                                    ->label(fn ($record) => 'Estado: ' . ucfirst($record->estado))
                                    ->schema([
                                        TextEntry::make('paciente.nombre_completo')
                                            ->label('◾ PACIENTE:')
                                            ->size('md'),

                                        TextEntry::make('paciente.obraSocial.id')
                                            ->label('◾ OBRA SOCIAL:')
                                            ->formatStateUsing(function ($record) {
                                                $obra = $record->paciente->obraSocial;

                                                if (! $obra) {
                                                    return 'Sin obra social';
                                                }

                                                return "{$obra->alias} - {$obra->nombre}";
                                            })
                                            ->badge()
                                            ->color('primary'),

                                        TextEntry::make('fecha')
                                            ->label('◾ FECHA:')
                                            ->date('d/m/Y')
                                            ->badge()
                                            ->color('primary'),

                                        TextEntry::make('hora')
                                            ->label('◾ HORA:')
                                            ->badge()
                                            ->color('primary'),

                                        TextEntry::make('observaciones')
                                            ->label('◾ OBSERVACIONES:')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Tab::make('Datos del Paciente')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        TextEntry::make('paciente.dni')->label('◾ DNI:'),
                                        TextEntry::make('paciente.fecha_nacimiento')->label('◾ FECHA DE NACIMIENTO:')->date('d/m/Y'),

                                        TextEntry::make('paciente.domicilio')->label('◾ DOMICILIO:')->placeholder('No informado'),
                                        TextEntry::make('paciente.telefono')->label('◾ TELÉFONO:')->placeholder('No informado'),

                                        TextEntry::make('paciente.estado_civil')
                                            ->label('◾ ESTADO CIVIL:')
                                            ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'No informado'),

                                        TextEntry::make('paciente.ocupacion')->label('◾ OCUPACIÓN:')->placeholder('No informada'),
                                    ])
                                    ->columns(2),
                                Tab::make('Información Médica')
                                    ->icon('heroicon-o-heart')
                                    ->schema([

                                        IconEntry::make('paciente.alergias')->label('◾ ALERGIA:')->boolean(),
                                        IconEntry::make('paciente.cirugias')->label('◾ CIRUGÍA')->boolean(),

                                        TextEntry::make('paciente.detalle_alergias')
                                            ->label('◾ DETALLE ALERGIA:')
                                            ->visible(fn ($record) => $record->paciente->alergias),

                                        TextEntry::make('paciente.detalle_cirugias')
                                            ->label('◾ DETALLE CIRUGÍA:')
                                            ->visible(fn ($record) => $record->paciente->cirugias),

                                        TextEntry::make('paciente.enfermedades_hereditarias')
                                            ->label('◾ ENFERMEDADES HEREDITARIAS:'),

                                        TextEntry::make('paciente.medicacion_actual')
                                            ->label('◾ MEDICACIÓN ACTUAL:'),

                                        TextEntry::make('paciente.peso')->label('◾ PESO (kg):')
                                            ->badge()
                                            ->color('primary'),

                                        TextEntry::make('paciente.presion_arterial')->label('◾ PRESIÓN ARTERIAL:')
                                            ->badge()
                                            ->color('primary'),
                                    ])
                                    ->columns(2),
                            ]),
                    ]),

                EditAction::make()->label('Editar'),
            ])

            ->bulkActions([
                DeleteBulkAction::make()->label('Eliminar seleccionados'),
            ])

            ->defaultSort('fecha', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    // ✅ Páginas del recurso
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTurnos::route('/'),
            'create' => Pages\CreateTurno::route('/create'),
            'edit' => Pages\EditTurno::route('/{record}/edit'),
        ];
    }
}