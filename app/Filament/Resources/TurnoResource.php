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
    DeleteBulkAction
};

/**
 * INFOLIST (VIEW)
 */
use Filament\Infolists\Components\{
    TextEntry,
    IconEntry,
    Tabs,
    Tabs\Tab,
    ImageEntry
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
                ->options([
                    'radiografia' => 'Radiografía',
                    'analisis_sangre' => 'Análisis de sangre',
                    'ecografia' => 'Ecografía',
                    'resonancia' => 'Resonancia',
                ])
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

                SelectColumn::make('estado')
                    ->options([
                        'confirmado' => 'Confirmado',
                        'cancelado' => 'Cancelado',
                        'atendido' => 'Atendido',
                    ])
                    ->afterStateUpdated(function ($record, $state) {

                        if ($state === 'atendido') {
                            \Filament\Notifications\Notification::make()
                                ->title('Completar atención')
                                ->body('Debe completar la observación médica')
                                ->warning()
                                ->send();
                        }
                    }),
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
                                        ->label('Paciente'),

                                    TextEntry::make('fecha')
                                        ->date('d/m/Y')
                                        ->label('Fecha'),

                                    TextEntry::make('hora')
                                        ->label('Hora'),

                                    TextEntry::make('estado')
                                        ->label('Estado'),

                                    TextEntry::make('motivo_consulta')
                                        ->label('Motivo'),

                                    TextEntry::make('observacion_medica')
                                        ->label('Observación del médico')
                                        ->visible(fn ($record) => $record->estado === 'atendido'),

                                    TextEntry::make('estudios')
                                        ->label('Estudios')
                                        ->formatStateUsing(fn ($state) => $state ? implode(', ', $state) : '-'),

                                ])
                                ->columns(2),

                            Tab::make('Paciente')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    TextEntry::make('paciente.dni')->label('DNI'),
                                    TextEntry::make('paciente.telefono')->label('Teléfono'),

                                    TextEntry::make('paciente.obraSocial.alias')
                                        ->label('Obra Social'),

                                    ImageEntry::make('paciente.recetas')
                                        ->label('Recetas del paciente')
                                        ->stacked()
                                        ->height(80)
                                        ->disk('public') // 🔥 IMPORTANTE
                                        ->multiple(),
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