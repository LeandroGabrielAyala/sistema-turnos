<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObraSocialResource\Pages\CreateObraSocial;
use App\Filament\Resources\ObraSocialResource\Pages\ListObraSocials;
use App\Filament\Resources\ObraSocialResource\Pages\EditObraSocial;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use App\Models\ObraSocial;

class ObraSocialResource extends Resource
{
    protected static ?string $model = ObraSocial::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('alias')
                ->label('Alias de la Obra Social')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Debe ser un alias único para la obra social.')
                ->placeholder('Ej: OSDE, Swiss Medical, etc.')
                ->columnSpanFull(),

            TextInput::make('nombre')
                ->label('Nombre Completo de la Obra Social')
                ->placeholder('Ej: Obra Social para los Docentes...')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                TextColumn::make('alias')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make()->modalWidth('lg'),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListObraSocials::route('/'),
            'create' => CreateObraSocial::route('/create'),
            'edit' => EditObraSocial::route('/{record}/edit'),
        ];
    }
}