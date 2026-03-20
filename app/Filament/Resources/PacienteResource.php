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
    Tabs,
    Hidden,
    FileUpload
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
    DeleteBulkAction,
    ActionGroup
};

/**
 * INFOLIST (VIEW)
 */
use Filament\Infolists\Components\{
    TextEntry,
    IconEntry,
    Tabs as InfolistTabs,
    Tabs\Tab as InfolistTab,
    ImageEntry

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
    protected static ?string $navigationGroup = 'Pacientes';
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
                                ->label('Enfermedades Hereditarias')
                                ->columnSpanFull(),

                            Textarea::make('medicacion_actual')
                                ->label('Medicación Actual')
                                ->columnSpanFull(),

                            TextInput::make('peso')
                                ->label('Peso')
                                ->numeric()
                                ->suffix('kg')->columnSpan(1),

                            TextInput::make('presion_sistolica')
                                ->label('Sistólica')
                                ->numeric()
                                ->live()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if ($state && $get('presion_diastolica')) {
                                        $set('presion_arterial', $state . '/' . $get('presion_diastolica'));
                                    }
                                })->columnSpan(1),

                            TextInput::make('presion_diastolica')
                                ->label('Diastólica')
                                ->numeric()
                                ->live()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if ($state && $get('presion_sistolica')) {
                                        $set('presion_arterial', $get('presion_sistolica') . '/' . $state);
                                    }
                                })->columnSpan(1),

                            Hidden::make('presion_arterial'),

                            FileUpload::make('recetas')
                                ->label('Recetas médicas')
                                ->image()
                                ->multiple()
                                ->disk('public') // 🔥 OBLIGATORIO
                                ->directory('recetas')
                                ->visibility('public') // 🔥 IMPORTANTE
                                ->imagePreviewHeight('150')
                                ->columnSpanFull(),
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
                    ->formatStateUsing(fn ($state) => $state ? $state . ' mmHg' : '-')
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
                    
                Filter::make('apellido')
                    ->label('Buscar por apellido')
                    ->form([
                        TextInput::make('apellido'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['apellido']) {
                            $query->where('apellido', 'like', '%' . $data['apellido'] . '%');
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
                ActionGroup::make([
                    /**
                     * VER
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
                        ->infolist([
                            InfolistTabs::make('Paciente')
                                ->tabs([

                                    InfolistTab::make('Datos Personales')
                                        ->icon('heroicon-o-user')
                                        ->schema([
                                            TextEntry::make('nombre_completo')
                                                ->label('◾ PACIENTE:')
                                                ->state(fn ($record) => "{$record->apellido}, {$record->nombre}"),

                                            TextEntry::make('dni')->label('◾ DNI:'),

                                            TextEntry::make('edad')
                                                ->label('◾ EDAD:')
                                                ->suffix(' años'),

                                            TextEntry::make('fecha_nacimiento')
                                                ->label('◾ FECHA DE NACIMIENTO:')
                                                ->date('d/m/Y'),

                                            TextEntry::make('obraSocial.alias')
                                                ->label('◾ OBRA SOCIAL:')
                                                ->badge()
                                                ->color('info'),
                                        ])
                                        ->columns(2),

                                    InfolistTab::make('Información Social')
                                        ->icon('heroicon-o-home')
                                        ->schema([
                                            TextEntry::make('estado_civil')->label('◾ ESTADO CIVIL:'),
                                            TextEntry::make('ocupacion')->label('◾ OCUPACIÓN:'),
                                            TextEntry::make('domicilio')->label('◾ DOMICILIO:'),
                                            TextEntry::make('telefono')->label('◾ TELÉFONO:'),
                                        ])
                                        ->columns(2),

                                    InfolistTab::make('Información Médica')
                                        ->icon('heroicon-o-heart')
                                        ->schema([
                                            IconEntry::make('alergias')->label('◾ ALERGIA:')->boolean(),
                                            IconEntry::make('cirugias')->label('◾ CIRUGÍA:')->boolean(),

                                            TextEntry::make('detalle_alergias')
                                                ->visible(fn ($record) => $record->alergias)
                                                ->label('◾ DETALLE ALERGIA:'),

                                            TextEntry::make('detalle_cirugias')
                                                ->visible(fn ($record) => $record->cirugias)
                                                ->label('◾ DETALLE CIRUGÍA:'),

                                            TextEntry::make('peso')
                                                ->label('◾ PESO:')
                                                ->suffix(' kg')
                                                ->badge()
                                                ->color('primary'),

                                            TextEntry::make('presion_arterial')
                                                ->label('◾ PRESIÓN ARTERIAL:')
                                                ->formatStateUsing(fn ($state) => $state ? $state . ' mmHg' : '-')
                                                ->badge()
                                                ->color('primary'),

                                            ImageEntry::make('recetas')
                                                ->label('Recetas')
                                                ->stacked()
                                                ->height(100),
                                        ])
                                        ->columns(2),
                                ]),
                        ]),

                    /**
                     * EDITAR
                     */
                    EditAction::make()
                        ->label('Editar'),

                    /**
                     * ELIMINAR
                     */
                    DeleteAction::make()
                        ->label('Eliminar')
                        ->modalHeading('Eliminar paciente')
                        ->modalDescription('¿Estás seguro de eliminar este paciente?')
                        ->modalSubmitActionLabel('Sí, eliminar'),
                ])
                ->icon('heroicon-m-ellipsis-vertical') // ← los 3 puntitos
                ->label('')
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
