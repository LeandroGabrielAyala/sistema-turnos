<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacienteResource\Pages\ListPacientes;
use App\Filament\Resources\PacienteResource\Pages\CreatePaciente;
use App\Filament\Resources\PacienteResource\Pages\EditPaciente;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Paciente;
use App\Models\ObraSocial;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Carbon\Carbon;

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static ?string $modelLabel = 'Paciente';
    protected static ?string $pluralModelLabel = 'Pacientes';
    protected static ?string $navigationLabel = 'Pacientes';
    protected static ?string $slug = 'pacientes';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Gestión Clínica';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'apellido';

    // 🔎 Búsqueda global del sitio
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'apellido', 'dni', 'telefono'];
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
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Datos Personales')
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
                        ->relationship(
                            name: 'obraSocial',
                            titleAttribute: 'alias'
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) => 
                            "{$record->alias} - {$record->nombre}"
                        )
                        ->searchable()
                        ->preload()
                ])->columns(2),

            Section::make('Información Social')
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
                ])->columns(2),

            Section::make('Información Médica')
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
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Paciente')
                    ->searchable(['apellido', 'nombre'])
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('apellido', $direction)
                                    ->orderBy('nombre', $direction);
                    }),

                TextColumn::make('dni')->label('DNI'),

                TextColumn::make('edad')
                    ->label('Edad')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('obraSocial.alias')
                    ->label('Obra Social'),

                TextColumn::make('telefono')->label('Teléfono')
                ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('estado_civil')
                    ->label('Estado Civil')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('alergias')->boolean()->label('Alergias'),
                IconColumn::make('cirugias')->boolean()->label('Cirugías'),
                TextColumn::make('peso')->suffix(' kg')->label('Peso')->badge()->color('primary'),
                TextColumn::make('presion_arterial')->label('Presión arterial')->badge()->color('primary'),
            ])

            ->filters([

                // 🔹 Filtro por Obra Social
                SelectFilter::make('obra_social_id')
                    ->relationship('obraSocial', 'alias'),

                // 🔹 Filtro por Edad
                Filter::make('edad_mayor_que')
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

                // 🔹 Filtro por nombre manual
                Filter::make('nombre')
                    ->form([
                        TextInput::make('nombre'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['nombre']) {
                            $query->where('nombre', 'like', '%' . $data['nombre'] . '%');
                        }
                    }),
            ])

            ->actions([
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
                        Tabs::make('Tabs')
                            ->tabs([

                                Tab::make('Datos Personales')
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
                                        TextEntry::make('obra_social')
                                            ->label('◾ OBRA SOCIAL:')
                                            ->state(fn ($record) => 
                                                $record->obraSocial 
                                                    ? "{$record->obraSocial->alias} - {$record->obraSocial->nombre}"
                                                    : '-'
                                            )
                                            ->badge()
                                            ->color('info'),
                                    ])
                                    ->columns(2),

                                Tab::make('Información Social')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        TextEntry::make('estado_civil')->label('◾ ESTADO CIVIL:'),
                                        TextEntry::make('ocupacion')->label('◾ OCUPACIÓN:'),
                                        TextEntry::make('domicilio')->label('◾ DOMICILIO:'),
                                        TextEntry::make('telefono')->label('◾ TELÉFONO:'),
                                    ])
                                    ->columns(2),

                                Tab::make('Información Médica')
                                    ->icon('heroicon-o-heart')
                                    ->schema([
                                        IconEntry::make('alergias')->boolean()->label('◾ ALERGIA:'),
                                        IconEntry::make('cirugias')->boolean()->label('◾ CIRUGÍA:'),
                                        TextEntry::make('detalle_alergias')
                                            ->visible(fn ($record) => $record->alergias)->label('◾ DETALLE ALERGIA:'),
                                        TextEntry::make('detalle_cirugias')
                                            ->visible(fn ($record) => $record->cirugias)->label('◾ DETALLE CIRUGÍA:'),
                                        TextEntry::make('peso')
                                            ->suffix(' kg')->label('◾ PESO:')->badge()
                                            ->color('info'),
                                        TextEntry::make('presion_arterial')->label('◾ PRESIÓN ARTERIAL:')->badge()
                                            ->color('info'),
                                    ])
                                    ->columns(2),
                            ]),
                    ]),

                EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ExportBulkAction::make()->label('Exportar seleccionados'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPacientes::route('/'),
            'create' => CreatePaciente::route('/crear'),
            'edit' => EditPaciente::route('/{record}/editar'),
        ];
    }
}