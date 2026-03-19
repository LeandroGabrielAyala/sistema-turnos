<?php

namespace App\Filament\Resources;

use App\Models\ObraSocial;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;

use Filament\Resources\Resource;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

use App\Filament\Resources\ObraSocialResource\Pages\ListObraSocials;
use App\Filament\Resources\ObraSocialResource\Pages\CreateObraSocial;
use App\Filament\Resources\ObraSocialResource\Pages\EditObraSocial;

class ObraSocialResource extends Resource
{
    /**
     * Modelo asociado
     */
    protected static ?string $model = ObraSocial::class;

    /**
     * Configuración del menú lateral
     */
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationLabel = 'Obras Sociales';
    protected static ?int $navigationSort = 2;

    /**
     * Badge con contador
     */
    public static function getNavigationBadge(): ?string
    {
        return ObraSocial::count();
    }

    /**
     * Color del badge (opcional)
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    /**
     * FORMULARIO (Crear / Editar)
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('alias')
                ->label('Alias')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Debe ser único.')
                ->placeholder('Ej: OSDE, Swiss Medical')
                ->columnSpanFull(),

            TextInput::make('nombre')
                ->label('Nombre completo')
                ->required()
                ->placeholder('Ej: Obra Social de...')
                ->columnSpanFull(),
        ]);
    }

    /**
     * TABLA
     */
    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Buscar obra social...')
            ->columns([
                TextColumn::make('alias')
                    ->label('Alias')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
            ])

            /**
             * ACCIONES
             */
            ->actions([
                /**
                 * VER (Modal + Infolist)
                 */
                ViewAction::make()
                    ->label('Ver')
                    ->modalHeading('Detalle de la Obra Social')
                    ->modalWidth('lg'),

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
                    ->modalHeading('Eliminar obra social')
                    ->modalDescription('¿Estás seguro de eliminar este registro?')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ]);
    }

    /**
     * INFOLIST (para el View)
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('alias')
                    ->label('Alias')
                    ->columnSpanFull(),

                TextEntry::make('nombre')
                    ->label('Nombre completo')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * BÚSQUEDA GLOBAL (CTRL + K en Filament)
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['alias', 'nombre'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->alias;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Nombre' => $record->nombre,
        ];
    }

    /**
     * RUTAS DE PÁGINAS
     */
    public static function getPages(): array
    {
        return [
            'index' => ListObraSocials::route('/'),
            'create' => CreateObraSocial::route('/create'),
            'edit' => EditObraSocial::route('/{record}/edit'),
        ];
    }
}