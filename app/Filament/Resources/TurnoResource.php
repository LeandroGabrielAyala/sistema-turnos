<?php

namespace App\Filament\Resources;

use App\Models\Turno;
use App\Filament\Resources\TurnoResource\Pages;
use App\Filament\Resources\TurnoResource\Widgets\CalendarioTurnos;

use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * COMPONENTES DE FORMULARIO
 */
use Filament\Forms\Components\{
    Select,
    DatePicker,
    TimePicker,
    Textarea
};

/**
 * COMPONENTES DE TABLA
 */
use Filament\Tables\Columns\{
    TextColumn,
    BadgeColumn,
    SelectColumn
};

/**
 * FILTROS
 */
use Filament\Tables\Filters\{
    SelectFilter,
    Filter
};

/**
 * ACCIONES
 */
use Filament\Tables\Actions\{
    ViewAction,
    EditAction,
    DeleteAction,
    DeleteBulkAction,
    Action
};

/**
 * INFOLIST (VIEW)
 */
use Filament\Infolists\Components\{
    TextEntry,
    IconEntry,
    Tabs,
    Tabs\Tab,
    ImageEntry,
    ViewEntry
};

class TurnoResource extends Resource
{
    /**
     * Modelo asociado
     */
    protected static ?string $model = Turno::class;

    /**
     * Configuración general
     */
    protected static ?string $modelLabel = 'Turno';
    protected static ?string $pluralModelLabel = 'Turnos';
    protected static ?string $navigationLabel = 'Turnos';
    protected static ?string $slug = 'turnos';

    /**
     * Configuración del menú
     */
    protected static ?string $navigationGroup = 'Agenda';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Campo principal del registro
     */
    protected static ?string $recordTitleAttribute = 'fecha';

    /**
     * 🔎 BÚSQUEDA GLOBAL (CTRL + K)
     */
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

    /**
     * Badge con contador
     */
    public static function getNavigationBadge(): ?string
    {
        return Turno::count();
    }

    /**
     * Color del badge (opcional)
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    /**
     * 📅 WIDGETS (Calendario)
     */
    public static function getWidgets(): array
    {
        return [
            CalendarioTurnos::class,
        ];
    }

    /**
     * 🧾 FORMULARIO (Crear / Editar Turno)
     */
    public static function form(Form $form): Form
    {
        return $form->schema([

            /**
             * Selección de paciente
             */
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

            /**
             * Estado del turno
             */
            Select::make('estado')
                ->options([
                    'confirmado' => 'Confirmado',
                    'cancelado' => 'Cancelado',
                    'atendido' => 'Atendido',
                ])
                ->default('confirmado')
                ->live(),

            /**
             * Motivo de la Consulta
             */
            Textarea::make('motivo_consulta')
                ->label('Motivo de consulta')
                ->columnSpanFull(),

            /**
             * Fecha del turno
             */
            DatePicker::make('fecha')
                ->label('Fecha')
                ->required()
                ->minDate(now()),

            /**
             * Hora del turno
             */
            TimePicker::make('hora')
                ->label('Hora')
                ->seconds(false)
                ->required(),

            /**
             * Observación del médico
             */
            Textarea::make('observacion_medica')
                ->label('Observación del médico')
                ->visible(fn ($get) => $get('estado') === 'atendido')
                ->columnSpanFull(),

            /**
             * Estudios solicitados
             */
            Select::make('estudios')
                ->label('Estudios solicitados')
                ->multiple()
                ->options(Turno::ESTUDIOS)
                ->visible(fn ($get) => $get('estado') === 'atendido')
                ->columnSpanFull(),
        ]);
    }

    /**
     * 📊 TABLA DE TURNOS
     */
    public static function table(Table $table): Table
    {
        return $table
            ->searchable()

            /**
             * 🔹 COLUMNAS
             */
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('hora')
                    ->label('Hora')
                    ->sortable(),

                TextColumn::make('paciente.nombre_completo')
                    ->label('Paciente')
                    ->searchable(['pacientes.nombre', 'pacientes.apellido'])
                    ->sortable(query: function (Builder $query, string $direction) {
                        return $query
                            ->orderBy(
                                \App\Models\Paciente::select('apellido')
                                    ->whereColumn('pacientes.id', 'turnos.paciente_id'),
                                $direction
                            )
                            ->orderBy(
                                \App\Models\Paciente::select('nombre')
                                    ->whereColumn('pacientes.id', 'turnos.paciente_id'),
                                $direction
                            );
                    }),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'info' => 'confirmado',
                        'danger' => 'cancelado',
                        'success' => 'atendido',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'confirmado',
                        'heroicon-o-x-circle' => 'cancelado',
                        'heroicon-o-check-circle' => 'atendido',
                    ]),
            ])

            /**
             * 🔹 FILTROS
             */
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'cancelado' => 'Cancelado',
                        'atendido' => 'Atendido',
                    ]),

                SelectFilter::make('paciente_id')
                    ->label('Paciente')
                    ->relationship('paciente', 'apellido'),

                Filter::make('fecha')
                    ->label('Fecha específica')
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

            /**
             * 🔹 ACCIONES POR FILA
             */
            ->actions([
                /**
                 * ATENDER
                 */

                Action::make('atender')
                    ->label('Atender')
                    ->modalHeading(fn ($record) => 
                        'ATENDIDO - Completar el Turno de ' . $record->paciente->nombre_completo
                    )
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado === 'confirmado')
                    ->form([
                        Textarea::make('observacion_medica')
                            ->label('Observación del Médico:')
                            ->required()
                            ->columnSpanFull(),

                        Select::make('estudios')
                            ->label('Estudios a realizar:')
                            ->multiple()
                            ->options(Turno::ESTUDIOS)
                            ->placeholder('Sin estudios solicitados')
                            ->helperText('Si no selecciona ninguno, se guardará como: "Sin estudios solicitados"')
                            ->columnSpanFull(),
                    ])
                    ->action(function ($record, $data) {

                        $record->update([
                            'estado' => 'atendido',
                            'observacion_medica' => $data['observacion_medica'],
                            'estudios' => array_values($data['estudios'] ?? []),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Turno Atendido Correctamente')
                            ->success()
                            ->send();
                    }),

                /**
                 * 👁 VER
                 */
                ViewAction::make()
                    ->label('Ver')
                    ->modalHeading(fn ($record) =>
                        'Detalle del Turno - ' .
                        $record->fecha->format('d/m/Y') . ' ' .
                        $record->hora
                    )
                    ->modalWidth('4xl')
                    ->infolist([
                        Tabs::make('Tabs')
                            ->tabs([

                                Tab::make('Estado')
                                    ->icon('heroicon-o-bookmark')
                                    ->schema([
                                        TextEntry::make('paciente.nombre_completo')
                                            ->label('◾ PACIENTE'),

                                        TextEntry::make('fecha')
                                            ->date('d/m/Y')
                                            ->label('◾ FECHA'),

                                        TextEntry::make('hora')
                                            ->label('◾ HORA'),

                                        TextEntry::make('estado')
                                            ->label('◾ ESTADO')
                                            ->badge()
                                            ->color(fn ($state) => match ($state) {
                                                'confirmado' => 'info',
                                                'cancelado' => 'danger',
                                                'atendido' => 'success',
                                                default => 'gray',
                                            })
                                            ->icons([
                                                'heroicon-o-clock' => 'confirmado',
                                                'heroicon-o-x-circle' => 'cancelado',
                                                'heroicon-o-check-circle' => 'atendido',
                                            ]),

                                        TextEntry::make('motivo_consulta')
                                            ->label('◾ MOTIVO TURNO'),

                                        TextEntry::make('observacion_medica')
                                            ->label('◾ OBSERVACIÓN MÉDICO')
                                            ->visible(fn ($record) => $record->estado === 'atendido'),

                                        TextEntry::make('estudios_formateados')
                                            ->label('◾ ESTUDIOS')
                                            ->visible(fn ($record) => $record->estado === 'atendido')
                                            ->badge()
                                            ->placeholder('Sin estudios solicitados')
                                            ->separator(','),

                                    ])
                                    ->columns(2),

                                Tab::make('Paciente')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        TextEntry::make('paciente.dni')
                                            ->label('◾ DNI:'),
                                            
                                        TextEntry::make('paciente.telefono')
                                            ->label('◾ TELÉFONO'),

                                        TextEntry::make('paciente.obraSocial.alias')
                                            ->label('◾ OBRA SOCIAL')
                                            ->formatStateUsing(function ($state, $record) {
                                                $obra = $record->paciente?->obraSocial;

                                                return $obra
                                                    ? "{$obra->alias} - {$obra->nombre}"
                                                    : '-';
                                            }),

                                        ViewEntry::make('paciente.recetas')
                                            ->label('◾ RECETA:')
                                            ->view('filament.components.recetas-preview')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                            ]),
                    ]),

                /**
                 * ✏️ EDITAR
                 */
                EditAction::make()
                    ->label('Editar'),

                /**
                 * ❌ ELIMINAR (NUEVO)
                 */
                DeleteAction::make()
                    ->label('Eliminar')
                    ->modalHeading('Eliminar turno')
                    ->modalDescription('¿Estás seguro de eliminar este turno?')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ])

            /**
             * 🔹 ACCIONES MASIVAS
             */
            ->bulkActions([
                DeleteBulkAction::make()->label('Eliminar seleccionados'),
            ])

            /**
             * Orden por defecto
             */
            ->defaultSort('fecha', 'desc');
    }

        /**
         * REDIRECCIONAR A LIST
         */
        public static function getRedirectUrl(): string
        {
            return static::getUrl('index');
        }

    /**
     * 🔗 RELACIONES (si hay RelationManagers)
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * 📄 PÁGINAS DEL RECURSO
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTurnos::route('/'),
            'create' => Pages\CreateTurno::route('/create'),
            'edit' => Pages\EditTurno::route('/{record}/edit'),
        ];
    }
}