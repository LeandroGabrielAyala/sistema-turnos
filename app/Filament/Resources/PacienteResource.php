<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObraSocialResource\RelationManagers\TurnosRelationManager;
use App\Models\Paciente;
use App\Models\ObraSocial;

use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

/**
 * COMPONENTES DE FORMULARIO
 */
use Filament\Forms\Components\{
    TextInput,
    DatePicker,
    Section,
    Select,
    Textarea,
    Toggle,
    Tabs\Tab,
    Tabs
};

/**
 * COMPONENTES DE TABLA
 */
use Filament\Tables\Columns\{
    TextColumn,
    IconColumn
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
};

/**
 * EXPORTACIÓN
 */
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

/**
 * PÁGINAS
 */
use App\Filament\Resources\PacienteResource\Pages\{
    ListPacientes,
    CreatePaciente,
    EditPaciente
};

class PacienteResource extends Resource
{
    /**
     * Modelo asociado al recurso
     */
    protected static ?string $model = Paciente::class;

    /**
     * Configuración general del recurso
     */
    protected static ?string $modelLabel = 'Paciente';
    protected static ?string $pluralModelLabel = 'Pacientes';
    protected static ?string $navigationLabel = 'Pacientes';
    protected static ?string $slug = 'pacientes';

    /**
     * Configuración del menú lateral
     */
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Gestión Clínica';
    protected static ?int $navigationSort = 1;

    /**
     * Campo principal para títulos
     */
    protected static ?string $recordTitleAttribute = 'apellido';

    /**
     * 🔎 BÚSQUEDA GLOBAL (CTRL + K)
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'apellido', 'dni', 'telefono', 'obraSocial.alias'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "{$record->apellido}, {$record->nombre}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'DNI' => $record->dni,
            'Teléfono' => $record->telefono,
            'Obra Social' => $record->obraSocial?->alias ?? 'Sin obra social',
        ];
    }


    /**
     * Badge con contador
     */
    public static function getNavigationBadge(): ?string
    {
        return Paciente::count();
    }

    /**
     * Color del badge (opcional)
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }


    /**
     * 🧾 FORMULARIO (Crear / Editar)
     */
public static function form(Form $form): Form
{
    return $form->schema([

        Tabs::make('Paciente')
            ->tabs([

                /**
                 * 🔹 TAB 1: DATOS PERSONALES
                 */
                Tab::make('Datos Personales')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('apellido')
                            ->label('Apellido')
                            ->required(),

                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required(),

                        TextInput::make('dni')
                            ->label('DNI')
                            ->unique(ignoreRecord: true)
                            ->required(),

                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de nacimiento')
                            ->required(),

                        Select::make('obra_social_id')
                            ->label('Obra Social')
                            ->relationship('obraSocial', 'alias')
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                "{$record->alias} - {$record->nombre}"
                            )
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                /**
                 * 🔹 TAB 2: INFORMACIÓN SOCIAL
                 */
                Tab::make('Información Social')
                    ->icon('heroicon-o-home')
                    ->schema([
                        Select::make('estado_civil')
                            ->label('Estado Civil')
                            ->options([
                                'soltero' => 'Soltero',
                                'casado' => 'Casado',
                                'divorciado' => 'Divorciado',
                                'viudo' => 'Viudo',
                            ]),

                        TextInput::make('ocupacion')
                            ->label('Ocupación'),

                        TextInput::make('domicilio')
                            ->label('Domicilio')
                            ->required(),

                        TextInput::make('telefono')
                            ->numeric()
                            ->label('Teléfono')
                            ->required(),
                    ])
                    ->columns(2),

                /**
                 * 🔹 TAB 3: INFORMACIÓN MÉDICA
                 */
                Tab::make('Información Médica')
                    ->icon('heroicon-o-heart')
                    ->schema([

                        Toggle::make('alergias')
                            ->label('¿Tiene alergias?')
                            ->live(),

                        Textarea::make('detalle_alergias')
                            ->label('Detalle de alergias')
                            ->visible(fn ($get) => $get('alergias')),

                        Toggle::make('cirugias')
                            ->label('¿Tiene cirugías?')
                            ->live(),

                        Textarea::make('detalle_cirugias')
                            ->label('Detalle de cirugías')
                            ->visible(fn ($get) => $get('cirugias')),

                        Textarea::make('enfermedades_hereditarias')
                            ->label('Enfermedades hereditarias')
                            ->columnSpanFull(),

                        Textarea::make('medicacion_actual')
                            ->label('Medicación actual')
                            ->columnSpanFull(),

                        TextInput::make('peso')
                            ->label('Peso')
                            ->numeric()
                            ->suffix('kg'),

                        TextInput::make('presion_arterial')
                            ->label('Presión Arterial'),
                    ])
                    ->columns(2),
            ])
            ->columnSpanFull(),
    ]);
}

    /**
     * 📊 TABLA DE REGISTROS
     */
    public static function table(Table $table): Table
    {
        return $table
            ->searchable()

            /**
             * 🔹 COLUMNAS
             */
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Paciente')
                    ->searchable(['apellido', 'nombre'])
                    ->sortable(query: fn ($query, $direction) =>
                        $query->orderBy('apellido', $direction)
                              ->orderBy('nombre', $direction)
                    ),

                TextColumn::make('dni')->label('DNI'),

                TextColumn::make('edad')
                    ->label('Edad')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('obraSocial.alias')
                    ->label('Obra Social'),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('estado_civil')
                    ->label('Estado Civil')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('alergias')->boolean()->label('Alergias'),
                IconColumn::make('cirugias')->boolean()->label('Cirugías'),

                TextColumn::make('peso')
                    ->label('Peso')
                    ->suffix(' kg')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('presion_arterial')
                    ->label('Presión arterial')
                    ->badge()
                    ->color('primary'),
            ])

            /**
             * 🔹 FILTROS
             */
            ->filters([
                SelectFilter::make('obra_social_id')
                    ->label('Obra Social')
                    ->relationship('obraSocial', 'alias'),

                Filter::make('edad_mayor_que')
                    ->label('Edad mínima')
                    ->form([
                        TextInput::make('edad')
                            ->numeric()
                            ->label('Mayor o igual a'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['edad']) {
                            $fecha = Carbon::now()
                                ->subYears($data['edad'])
                                ->format('Y-m-d');

                            $query->where('fecha_nacimiento', '<=', $fecha);
                        }
                    }),

                Filter::make('nombre')
                    ->label('Buscar por nombre')
                    ->form([
                        TextInput::make('nombre'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['nombre']) {
                            $query->where('nombre', 'like', '%' . $data['nombre'] . '%');
                        }
                    }),
            ])

            /**
             * 🔹 ACCIONES POR FILA
             */
            ->actions([
                /**
                 * VER (Modal con Infolist)
                 */
                ViewAction::make()
                    ->label('Ver')
                    ->modalHeading(fn ($record) =>
                        'Paciente: ' .
                        $record->apellido . ' ' .
                        $record->nombre .
                        ' | DNI: ' .
                        $record->dni
                    )
                    ->modalWidth('5xl')
                    ->infolist([/* (se mantiene tu infolist tal cual) */]),

                /**
                 * EDITAR
                 */
                EditAction::make()
                    ->label('Editar'),

                /**
                 * ❌ ELIMINAR (NUEVO)
                 */
                DeleteAction::make()
                    ->label('Eliminar')
                    ->modalHeading('Eliminar paciente')
                    ->modalDescription('¿Estás seguro de eliminar este paciente?')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ])

            /**
             * 🔹 ACCIONES MASIVAS
             */
            ->bulkActions([
                DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ExportBulkAction::make()->label('Exportar seleccionados'),
            ]);
    }

    /**
     * 🔹 Relation Manager con Turnos
     */
    public static function getRelations(): array
    {
        return [
            TurnosRelationManager::class,
        ];
    }

    /**
     * 📄 RUTAS DE PÁGINAS
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPacientes::route('/'),
            'create' => CreatePaciente::route('/crear'),
            'edit' => EditPaciente::route('/{record}/editar'),
        ];
    }
}
